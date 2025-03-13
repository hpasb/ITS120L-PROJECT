from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
import ollama
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain.vectorstores import Chroma
from langchain.embeddings import OllamaEmbeddings
from langchain.chains import RetrievalQA
from langchain.llms import Ollama
from langchain.schema import Document

# Initialize Flask app
app = Flask(__name__)
CORS(app)

# HopeBot Persona and Adoption Information
HOPEBOT_PERSONA = "I am HopeBot, an AI assistant for **Hope for Strays**. I help users find and adopt pets."
ADOPTION_INSTRUCTIONS = """
Adopting a pet is a rewarding experience that requires careful consideration.
1️⃣ Search for a pet matching your lifestyle on the adoption page.
2️⃣ View the pet’s profile (personality, health status, dietary needs).
3️⃣ Contact the shelter using the provided details.
4️⃣ Schedule a meet-and-greet (online or in-person).
5️⃣ Submit an adoption application.
6️⃣ Upon approval, finalize the adoption and bring your new pet home! 🏡
"""
HOPE_FOR_STRAYS_DESCRIPTION = """
Hope for Strays is a non-profit platform that connects adopters with rescue shelters. 
We showcase stray dogs and cats available for adoption, working with shelters and vets to ensure each pet finds a loving home.
"""

# Setup ChromaDB with HopeBot's Knowledge
def setup_vector_db():
    documents = [
        Document(page_content=HOPEBOT_PERSONA, metadata={"source": "HopeBot"}),
        Document(page_content=ADOPTION_INSTRUCTIONS, metadata={"source": "Adoption Guide"}),
        Document(page_content=HOPE_FOR_STRAYS_DESCRIPTION, metadata={"source": "Organization Info"})
    ]
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=500, chunk_overlap=50)
    texts = text_splitter.split_documents(documents)

    # Convert text into embeddings using TinyLlama
    vectorstore = Chroma.from_documents(texts, OllamaEmbeddings(model="tinyllama"))
    return vectorstore

vector_db = setup_vector_db()
retriever = vector_db.as_retriever()
qa_chain = RetrievalQA(llm=Ollama(model="tinyllama"), retriever=retriever)

# MySQL Database connection
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="1234",
        database="hope_for_strays"
    )

# Fetch pet details from MySQL
def get_pet_details(pet_name):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT Pet.*, Shelter.name AS shelter_name, Shelter.contact_num AS shelter_contact, Shelter.location AS shelter_location 
        FROM Pet JOIN Shelter ON Pet.shelterID = Shelter.shelterID 
        WHERE Pet.name = %s AND Pet.status = 'available'
    """, (pet_name,))
    pet = cursor.fetchone()
    conn.close()
    return pet

# Handle user queries
@app.route("/chat", methods=["POST"])
def chat():
    user_input = request.json.get("message")
    if not user_input:
        return jsonify({"reply": "I didn't understand that. Can you ask again?"})

    # Check if the user is asking about a pet
    if "tell me about" in user_input.lower():
        pet_name = user_input.lower().split("tell me about")[-1].strip()
        pet = get_pet_details(pet_name)
        if pet:
            return jsonify({"reply": f"🐾 Meet {pet['name']}!\n\n• Breed: {pet['breed']}\n• Type: {pet['type']}\n• Size: {pet['size']}\n• Age: {pet['age']}\n• Gender: {pet['gender']}\n• Vaccination: {pet['vaccination']}\n• Medical Conditions: {pet['medical_con']}\n• Dietary Needs: {pet['dietary_needs']}\n• Shelter: {pet['shelter_name']}\n• Location: {pet['shelter_location']}\n• Contact: {pet['shelter_contact']}"} )
        else:
            return jsonify({"reply": "Sorry, I couldn't find details on that pet."})

    # Use RAG for general queries
    response = qa_chain.run(user_input)
    return jsonify({"reply": response})

if __name__ == "__main__":
    app.run(debug=True)
