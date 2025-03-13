from flask import Flask, request, jsonify
import spacy
from flask_cors import CORS
import mysql.connector
import ollama  # TinyLlama integration

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable cross-origin requests

# Load spaCy NLP model for intent detection
nlp = spacy.load("en_core_web_sm")

# MySQL Database connection setup
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="1234",  
        database="hope_for_strays"
    )

# HopeBot's Persona
HOPEBOT_PERSONA = "I am HopeBot, an AI assistant for **Hope for Strays**. I help users find and adopt pets."

# Adoption Instructions and Organization Description
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

# Function to detect user intent
def get_intent(user_input):
    doc = nlp(user_input.lower())
    intents = {
        "greeting": ["hi", "hello"],
        "goodbye": ["bye", "goodbye"],
        "thanks": ["thank you"],
        "who_are_you": ["hopebot", "who are you"],
        "adoption_instructions": ["adoption process", "how to adopt"],
        "hope_for_strays_description": ["hope for strays", "what is this"],
        "available_pets": ["available pets", "adoptable pets", "dogs offered"],
        "available_cats": ["available cats", "cats available", "cats offered"],
        "available_dogs": ["available dogs", "dogs available"],
        "cat_breeds": ["breeds of cats"],
        "dog_breeds": ["breeds of dogs"],
        "pets_by_size": ["small pets", "medium pets", "large pets"],
        "pet_details": ["tell me about"]
    }
    for intent, keywords in intents.items():
        if any(keyword in doc.text for keyword in keywords):
            return intent
    return "general_query"

# Function to extract pet name from user input
def extract_pet_name(user_input):
    words = user_input.split()
    index = words.index("about") + 1 if "about" in words else -1
    return words[index] if index > 0 and index < len(words) else None

# Function to handle general queries using TinyLlama
def ask_tinyllama(user_input):
    response = ollama.chat(model="tinyllama", messages=[{"role": "user", "content": user_input}])
    return response["message"]["content"]

@app.route("/chat", methods=["POST"])
def chat():
    user_input = request.json.get("message")
    
    if not user_input:
        return jsonify({"reply": "I didn't understand that. Can you ask again?"})

    intent = get_intent(user_input)
    responses = {
        "greeting": "Hello! I am HopeBot, your assistant for pet adoption. How can I help you today?",
        "goodbye": "Goodbye! Hope to assist you again soon. Take care!",
        "thanks": "You're welcome! Let me know if you need anything else. 😊",
        "who_are_you": HOPEBOT_PERSONA,
        "adoption_instructions": ADOPTION_INSTRUCTIONS,
        "hope_for_strays_description": HOPE_FOR_STRAYS_DESCRIPTION
    }
    if intent in responses:
        return jsonify({"reply": responses[intent]})
    
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    if intent == "available_pets":
        cursor.execute("SELECT name, breed, size FROM Pet WHERE status = 'available' LIMIT 5")
    elif intent == "available_cats":
        cursor.execute("SELECT name, breed FROM Pet WHERE status = 'available' AND type = 'cat' LIMIT 5")
    elif intent == "available_dogs":
        cursor.execute("SELECT name, breed FROM Pet WHERE status = 'available' AND type = 'dog' LIMIT 5")
    elif intent == "cat_breeds":
        cursor.execute("SELECT DISTINCT breed FROM Pet WHERE status = 'available' AND type = 'cat'")
    elif intent == "dog_breeds":
        cursor.execute("SELECT DISTINCT breed FROM Pet WHERE status = 'available' AND type = 'dog'")
    elif intent == "pets_by_size":
        size = "small" if "small" in user_input.lower() else "medium" if "medium" in user_input.lower() else "large"
        cursor.execute("SELECT name, breed FROM Pet WHERE status = 'available' AND size = %s", (size,))
    elif intent == "pet_details":
        pet_name = extract_pet_name(user_input)
        if pet_name:
            cursor.execute("SELECT Pet.*, Shelter.name AS shelter_name, Shelter.contact_num AS shelter_contact, Shelter.location AS shelter_location FROM Pet JOIN Shelter ON Pet.shelterID = Shelter.shelterID WHERE Pet.name = %s AND Pet.status = 'available'", (pet_name,))
            pet = cursor.fetchone()
            conn.close()
            if pet:
                return jsonify({"reply": f"🐾 Meet {pet['name']}!\n\n\u2022 Breed: {pet['breed']}\n\u2022 Type: {pet['type']}\n\u2022 Size: {pet['size']}\n\u2022 Age: {pet['age']}\n\u2022 Gender: {pet['gender']}\n\u2022 Vaccination: {pet['vaccination']}\n\u2022 Medical Conditions: {pet['medical_con']}\n\u2022 Dietary Needs: {pet['dietary_needs']}\n\u2022 Shelter: {pet['shelter_name']}\n\u2022 Location: {pet['shelter_location']}\n\u2022 Contact: {pet['shelter_contact']}"})
            else:
                return jsonify({"reply": "Sorry, I couldn't find details on that pet."})
        else:
            return jsonify({"reply": "Please specify the pet's name after 'Tell me about'."})
    else:
        conn.close()
        return jsonify({"reply": ask_tinyllama(user_input)})

if __name__ == "__main__":
    app.run(debug=True)
