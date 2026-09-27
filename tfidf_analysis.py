import mysql.connector
from sklearn.feature_extraction.text import TfidfVectorizer


# =========================================================
# FEATURE KEYWORDS
# =========================================================

FEATURE_KEYWORDS = {

    "Nature": [
        "nature",
        "forest",
        "greenery",
        "scenery",
        "landscape",
        "waterfall"
    ],

    "Mountains": [
        "mountain",
        "mountains",
        "hill",
        "hills",
        "snow",
        "valley"
    ],

    "Beach": [
        "beach",
        "beaches",
        "sea",
        "ocean",
        "island",
        "coast"
    ],

    "Adventure": [
        "adventure",
        "trek",
        "trekking",
        "hiking",
        "camping",
        "rafting",
        "photography",
        "activities"
    ],

    "Peace": [
        "peaceful",
        "peace",
        "calm",
        "quiet",
        "relaxing",
        "relaxed",
        "serene"
    ],

    "Culture": [
        "culture",
        "historical",
        "history",
        "temple",
        "heritage",
        "traditional",
        "museum"
    ]
}


# =========================================================
# MYSQL CONNECTION
# =========================================================

connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="wanderwise"
)

cursor = connection.cursor(dictionary=True)


# =========================================================
# GET USER DIARIES
# =========================================================

user_id = 1

query = """
SELECT destination, experience
FROM trips
WHERE user_id = %s
AND experience IS NOT NULL
AND experience != ''
"""

cursor.execute(query, (user_id,))

trips = cursor.fetchall()


# =========================================================
# PREPARE DIARIES
# =========================================================

destinations = []
diaries = []

for trip in trips:

    destinations.append(trip["destination"])

    diaries.append(trip["experience"])


# =========================================================
# CHECK IF DIARIES EXIST
# =========================================================

if len(diaries) == 0:

    print("No travel diary entries found.")

    cursor.close()
    connection.close()

    exit()


# =========================================================
# TF-IDF
# =========================================================

vectorizer = TfidfVectorizer(
    stop_words="english"
)

tfidf_matrix = vectorizer.fit_transform(diaries)

words = vectorizer.get_feature_names_out()


# =========================================================
# FEATURE SCORES
# =========================================================

feature_scores = {

    "Nature": 0,
    "Mountains": 0,
    "Beach": 0,
    "Adventure": 0,
    "Peace": 0,
    "Culture": 0
}


print("\n==============================")
print("TF-IDF FEATURE ANALYSIS")
print("==============================")


# =========================================================
# ANALYZE EACH DIARY
# =========================================================

for i, destination in enumerate(destinations):

    scores = tfidf_matrix[i].toarray()[0]

    word_scores = list(zip(words, scores))

    word_scores.sort(
        key=lambda x: x[1],
        reverse=True
    )

    print("\nDestination:", destination)

    # -----------------------------------------------------
    # TOP TF-IDF WORDS
    # -----------------------------------------------------

    print("Important words:")

    for word, score in word_scores[:5]:

        print(
            word,
            ":",
            round(score, 3)
        )

    # -----------------------------------------------------
    # FEATURE MAPPING
    # -----------------------------------------------------

    destination_features = {

        "Nature": 0,
        "Mountains": 0,
        "Beach": 0,
        "Adventure": 0,
        "Peace": 0,
        "Culture": 0
    }


    for word, score in word_scores:

        word = word.lower().strip()

        matched_feature = None

        for feature, keywords in FEATURE_KEYWORDS.items():

            if word in keywords:

                matched_feature = feature
                break


        # Only add the score if a feature was actually matched

        if matched_feature is not None:

            destination_features[matched_feature] += score


    # -----------------------------------------------------
    # DISPLAY MAPPED FEATURES
    # -----------------------------------------------------

    print("Mapped features:")

    for feature, score in destination_features.items():

        if score > 0:

            print(
                feature,
                ":",
                round(score, 3)
            )

            feature_scores[feature] += score


# =========================================================
# FINAL TF-IDF PROFILE
# =========================================================

print("\n==============================")
print("FINAL TF-IDF FEATURE PROFILE")
print("==============================")


total_score = sum(
    feature_scores.values()
)


final_profile = {}


if total_score > 0:

    for feature, score in feature_scores.items():

        percentage = (
            score / total_score
        ) * 100

        final_profile[feature] = round(
            percentage,
            2
        )

        print(
            feature,
            ":",
            round(percentage, 2),
            "%"
        )

else:

    print("No feature matches found.")


# =========================================================
# MACHINE-READABLE OUTPUT FOR PHP
# =========================================================

print("\nTFIDF_PROFILE")

for feature, value in final_profile.items():

    print(
        feature + ":" + str(value)
    )


# =========================================================
# CLOSE CONNECTION
# =========================================================

cursor.close()

connection.close()