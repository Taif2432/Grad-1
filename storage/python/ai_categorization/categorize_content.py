import spacy
import sys
import json

# Load the English NLP model
nlp = spacy.load("en_core_web_sm")

# Original keyword rules
RAW_CATEGORIES = {
    "Anxiety": [
        "panic", "worry", "nervous", "anxious", "fear", "scared", "uneasy", "overthinking",
        "I can't calm down", "my heart races", "constant fear", "I feel on edge", 
        "social anxiety", "fear of the future", "I feel trapped", "suffocating fear"
    ],
    "Stress": [
        "stress", "stressed", "pressure", "burnout", "tension", "overwhelmed", "exhausted", 
        "too much on my plate", "can't focus", "tight deadlines", "I feel under pressure", 
        "no time to relax", "constant workload", "I'm mentally drained", "it's all too much"
    ],
    "Depression": [
        "sad", "hopeless", "depressed", "unmotivated", "tired", "empty", "nothing matters", 
        "I feel numb", "crying a lot", "I lost interest", "I don’t want to do anything", 
        "life feels pointless", "lack of energy", "no joy in life", "I feel worthless"
    ]
}

# Lemmatize all keywords once
CATEGORIES = {}
for category, keywords in RAW_CATEGORIES.items():
    doc = nlp(" ".join(keywords))
    CATEGORIES[category] = [token.lemma_.lower().strip() for token in doc]

def categorizeContentWithPython(text):
    doc = nlp(text.lower())
    scores = {category: 0 for category in CATEGORIES}

    for token in doc:
        lemma = token.lemma_.lower().strip()
        for category, lemmas in CATEGORIES.items():
            if lemma in lemmas:
                scores[category] += 1

    best_category = max(scores, key=scores.get)
    return best_category if scores[best_category] > 0 else "Uncategorized"

if __name__ == "__main__":
    try:
        # raw_input = sys.stdin.read()
        # print(f"RAW INPUT: {raw_input}", file=sys.stderr)  # Debug line
        # input_data = json.loads(raw_input)
        input_data = json.loads(sys.stdin.read())
        content_text = input_data.get("text", "")
        category = categorizeContentWithPython(content_text)
        print(category)
    except Exception:
        print("Uncategorized")
