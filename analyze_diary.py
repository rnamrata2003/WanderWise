import mysql.connector
import re


# -----------------------------
# FEATURE KEYWORDS
# -----------------------------

FEATURES = {
    "nature": [
        "nature", "forest", "greenery", "scenery",
        "landscape", "waterfall"
    ],

    "mountains": [
        "mountain", "mountains", "hill", "hills",
        "snow", "valley"
    ],

    "beach": [
        "beach", "beaches", "sea", "ocean",
        "island", "coast"
    ],

    "adventure": [
        "trek", "trekking", "hiking", "adventure",
        "camping", "rafting", "photography"
    ],

    "peace": [
        "peaceful", "peace", "calm", "quiet",
        "relaxing", "relaxed", "serene", "relax"
    ],

    "culture": [
        "culture", "historical", "history", "temple",
        "heritage", "traditional", "museum"
    ]
}


# -----------------------------
# SENTIMENT WORDS
# -----------------------------

POSITIVE_WORDS = [
    "love", "loved",
    "like", "liked",
    "enjoy", "enjoyed",
    "amazing",
    "beautiful",
    "excellent",
    "wonderful",
    "pleasant",
    "great",
    "fantastic"
]

NEGATIVE_WORDS = [
    "hate", "hated",
    "dislike", "disliked",
    "boring",
    "terrible",
    "bad",
    "crowded",
    "unpleasant",
    "stressful"
]


# -----------------------------
# NEGATIVE PHRASES
# -----------------------------

NEGATIVE_PHRASES = [
    "did not like",
    "didn't like",
    "did not enjoy",
    "didn't enjoy",
    "not relaxing",
    "not peaceful",
    "less relaxing",
    "less peaceful",
    "not enjoyable"
]


# -----------------------------
# ANALYZE ONE SENTENCE
# -----------------------------

def analyze_sentence(sentence):

    words = re.findall(r"\b[a-z]+\b", sentence.lower())

    scores = {
        "nature": 0,
        "mountains": 0,
        "beach": 0,
        "adventure": 0,
        "peace": 0,
        "culture": 0
    }

    for feature, keywords in FEATURES.items():

        for keyword in keywords:

            for i, word in enumerate(words):

                if word == keyword:

                    # Look around the feature word
                    start = max(0, i - 6)
                    end = min(len(words), i + 7)

                    context = words[start:end]

                    context_text = " ".join(context)

                    # -------------------------
                    # CHECK NEGATIVE PHRASES
                    # -------------------------

                    is_negative = False

                    for phrase in NEGATIVE_PHRASES:

                        if phrase in context_text:
                            is_negative = True
                            break

                    # -------------------------
                    # CHECK NEGATION
                    # -------------------------

                    if not is_negative:

                        if "not" in context or "never" in context:
                            is_negative = True

                    # -------------------------
                    # CHECK SENTIMENT WORDS
                    # -------------------------

                    positive_found = False
                    negative_found = False

                    for word2 in context:

                        if word2 in POSITIVE_WORDS:
                            positive_found = True

                        if word2 in NEGATIVE_WORDS:
                            negative_found = True

                    # -------------------------
                    # FINAL SCORE
                    # -------------------------

                    if is_negative or negative_found:
                        scores[feature] = -1

                    elif positive_found:
                        scores[feature] = 1

                    else:
                        # Feature mentioned but no clear sentiment
                        scores[feature] = 0

    return scores


# -----------------------------
# ANALYZE COMPLETE DIARY
# -----------------------------

def analyze_diary(text):

    total_scores = {
        "nature": 0,
        "mountains": 0,
        "beach": 0,
        "adventure": 0,
        "peace": 0,
        "culture": 0
    }

    sentences = re.split(r"[.!?]+", text)

    for sentence in sentences:

        if sentence.strip() == "":
            continue

        sentence_scores = analyze_sentence(sentence)

        for feature in total_scores:
            total_scores[feature] += sentence_scores[feature]

    return total_scores


# -----------------------------
# MYSQL CONNECTION
# -----------------------------

connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="wanderwise"
)

cursor = connection.cursor(dictionary=True)

user_id = 1


# -----------------------------
# GET USER DIARIES
# -----------------------------

query = """
SELECT destination, rating, experience
FROM trips
WHERE user_id = %s
AND experience IS NOT NULL
AND experience != ''
"""

cursor.execute(query, (user_id,))

trips = cursor.fetchall()


# -----------------------------
# NLP PROFILE
# -----------------------------

features = [
    "nature",
    "mountains",
    "beach",
    "adventure",
    "peace",
    "culture"
]

weighted_scores = {}

for feature in features:
    weighted_scores[feature] = 0


total_rating = 0


# -----------------------------
# ANALYZE EACH TRIP
# -----------------------------

for trip in trips:

    diary = trip["experience"]
    rating = trip["rating"]

    scores = analyze_diary(diary)

    print("\nDestination:", trip["destination"])
    print("Rating:", rating)
    print("NLP:", scores)

    for feature in features:
        weighted_scores[feature] += scores[feature] * rating

    total_rating += rating


# -----------------------------
# FINAL NLP PROFILE
# -----------------------------

print("\n==============================")
print("FINAL NLP PROFILE")
print("==============================")


if total_rating > 0:

    for feature in features:

        final_score = weighted_scores[feature] / total_rating

        print(
            feature.capitalize(),
            ":",
            round(final_score, 2)
        )


cursor.close()
connection.close()