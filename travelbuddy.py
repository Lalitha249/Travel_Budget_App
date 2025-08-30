from flask import Flask, render_template, request, jsonify

app = Flask(__name__)

# User session storage
user_data = {
    "preference": None,
    "destination": None,
    "days": None,
    "members": None,
    "trip_type": None
}

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/chat", methods=["POST"])
def chat():
    user_message = request.json.get("message", "").lower()
    response = "🤖 Sorry, I didn’t get that. Can you repeat?"

    # Greeting
    if any(word in user_message for word in ["hi", "hello", "hey", "hii"]):
        response = "🤖 Hello! I’m your Travel Buddy 🌍. Do you prefer a beach, mountain, or city trip? Or a place like 'Goa' or 'Paris'?"

    # Preference
    elif any(word in user_message for word in ["beach", "mountain", "city"]):
        user_data["preference"] = user_message
        response = f"🌟 Great! You chose {user_message}. How many days do you plan for the trip?"

    # Destination
    elif any(place in user_message for place in ["goa", "paris", "maldives", "bali", "manali"]):
        user_data["destination"] = user_message
        response = f"✨ {user_message.capitalize()} sounds amazing! How many days do you want to spend there?"

    # Number of days (only if not already set)
    elif (("day" in user_message or user_message.isdigit()) and user_data["days"] is None):
        try:
            days = int("".join([c for c in user_message if c.isdigit()]))
            user_data["days"] = days
            response = f"🗓️ Got it! {days} days trip. How many members are traveling?"
        except:
            response = "🤖 Please mention number of days like '3 days'."

    # Number of members (only if days are already set but members not yet set)
    elif (user_data["days"] is not None and user_data["members"] is None and user_message.isdigit()):
        try:
            members = int(user_message)
            user_data["members"] = members
            response = f"👨‍👩‍👧 Awesome! {members} members. Is it a family trip or with friends?"
        except:
            response = "🤖 Please mention the number of people clearly."

    # Trip type
    elif "family" in user_message or "friend" in user_message:
        user_data["trip_type"] = user_message
        destination_or_pref = user_data.get("destination") or user_data.get("preference", "Not set")
        response = (
            f"✅ Perfect! Here’s your travel plan:\n\n"
            f"🌍 Destination: {destination_or_pref.capitalize()}\n"
            f"🗓️ Days: {user_data['days']}\n"
            f"👥 Members: {user_data['members']}\n"
            f"🏡 Trip Type: {user_data['trip_type'].capitalize()}\n\n"
            f"✨ Day 1: Arrival + Local sightseeing\n"
            f"✨ Day 2: Adventure + Shopping\n"
            f"✨ Day 3: Relaxation + Departure\n"
        )

    return jsonify({"reply": response})

if __name__ == "__main__":
    app.run(debug=True)
    
