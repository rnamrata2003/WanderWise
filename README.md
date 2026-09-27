# 🌍 WanderWise

### AI-Powered Travel Diary & Personalized Recommendation System

WanderWise is a web-based travel diary application that allows users to record their travel experiences, rate previous trips, and receive personalized destination recommendations.

The project combines **PHP, MySQL, Python, NLP, and TF-IDF** to understand travel preferences from both numerical ratings and written diary experiences.

---

## 📌 What Does WanderWise Do?

WanderWise allows users to:

* 📝 Add and manage travel experiences
* ⭐ Rate previous trips
* 📊 Analyze travel preferences
* 🧠 Analyze diary experiences using NLP
* 🔤 Extract important travel-related terms using TF-IDF
* 🤖 Generate personalized destination recommendations
* 🚫 Avoid recommending destinations that the user has already visited

---

## 🧠 Where Is the AI/ML?

The AI/ML part of WanderWise focuses on analyzing the user's travel experiences and generating personalized recommendations.

### 1. ⭐ Rating-Based Preference Analysis

The system analyzes ratings given to previously visited destinations.

These ratings help identify the user's preferences across different travel categories:

* Nature
* Mountains
* Beach
* Adventure
* Peace
* Culture

### 2. 📝 NLP-Based Diary Analysis

The user's written travel experiences are analyzed using Natural Language Processing.

For example:

> "The mountains were beautiful and peaceful. I enjoyed trekking."

The system identifies relevant travel-related terms such as:

```text
mountains → Mountains
peaceful  → Peace
trekking  → Adventure
```

### 3. 🔤 TF-IDF Analysis

WanderWise uses **TF-IDF (Term Frequency–Inverse Document Frequency)** to identify important words from travel diary entries.

The extracted terms are mapped to travel categories such as:

```text
Nature
Mountains
Beach
Adventure
Peace
Culture
```

This provides additional information about the user's travel interests.

---

## 🔄 Recommendation Process

```text
        User Adds Travel Diary
                 ↓
           MySQL Database
                 ↓
       Rating-Based Analysis
                 +
           NLP Analysis
                 +
          TF-IDF Analysis
                 ↓
       Travel Preference Profile
                 ↓
      Destination Feature Matching
                 ↓
        Personalized Match Score
                 ↓
     Recommended Destinations
```

The recommendation system compares the user's travel preferences with destination characteristics and generates a personalized match percentage.

---

## 🛠️ Technologies Used

### Frontend

* HTML
* CSS
* JavaScript

### Backend

* PHP

### Database

* MySQL

### AI / ML

* Python
* Natural Language Processing (NLP)
* TF-IDF
* Keyword-based text analysis
* Similarity-based recommendation

### Development Environment

* XAMPP

---

## ✨ Main Features

### Travel Diary

Users can record:

* Destination
* Travel date
* Budget
* Rating
* Travel experience

### Travel History

Users can view their previously recorded trips and experiences.

### Preference Analysis

The system calculates preferences across six travel categories:

**Nature, Mountains, Beach, Adventure, Peace, and Culture**

### AI Travel Profile

Ratings and diary text are combined to build a personalized travel preference profile.

### Personalized Recommendations

The system recommends destinations based on how closely their characteristics match the user's preferences.

### Visited Destination Filtering

Destinations that the user has already visited are excluded from the recommendation results.

---

## 🎯 Project Objective

The objective of WanderWise is to demonstrate how a traditional travel diary application can be enhanced with **AI/NLP-based personalization**.

Instead of simply showing a list of popular destinations, WanderWise analyzes the user's previous experiences and preferences to generate personalized recommendations.

---

## 🚀 Future Improvements

* User authentication and multiple user profiles
* Larger destination dataset
* More advanced recommendation algorithms
* Improved NLP models
* Real-time destination information
* Weather and travel-cost integration
* More detailed travel preference analysis

---

## 👩‍💻 Project

**WanderWise – AI-Powered Travel Diary & Personalized Recommendation System**

An MCA project demonstrating the integration of **web development, database management, NLP, TF-IDF, and personalized recommendation systems**.

