# 🔥 Firebase Setup Guide — Hyperiocal Secondhand Market

Follow these steps carefully to connect your app to Firebase.

---

## Step 1 — Create a Firebase Project

1. Go to [https://console.firebase.google.com](https://console.firebase.google.com)
2. Click **"Add project"**
3. Enter project name: `hyperiocal-market` (or any name you like)
4. Disable Google Analytics (optional for now) → click **Create Project**
5. Wait for the project to be created → click **Continue**

---

## Step 2 — Register a Web App

1. On the Firebase project dashboard, click the **Web** icon (`</>`)
2. Enter app nickname: `Hyperiocal Web`
3. Click **"Register app"**
4. You will see a `firebaseConfig` object like this:

```js
const firebaseConfig = {
  apiKey: "AIza...",
  authDomain: "your-project.firebaseapp.com",
  projectId: "your-project-id",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123:web:abc123"
};
```

5. **Copy this entire object**

---

## Step 3 — Paste Config into `firebase-config.js`

1. Open `firebase-config.js` in your project
2. Replace the placeholder config block with **your** copied values:

```js
const firebaseConfig = {
  apiKey:            "YOUR_REAL_API_KEY",      // ← paste here
  authDomain:        "YOUR_PROJECT_ID.firebaseapp.com",
  projectId:         "YOUR_PROJECT_ID",
  storageBucket:     "YOUR_PROJECT_ID.appspot.com",
  messagingSenderId: "YOUR_SENDER_ID",
  appId:             "YOUR_APP_ID"
};
```

> ⚠️ Never commit your `firebase-config.js` to a public GitHub repo. Add it to `.gitignore`.

---

## Step 4 — Enable Firebase Authentication

1. In Firebase Console → left sidebar → **Authentication**
2. Click **"Get started"**
3. Under **Sign-in method**, click **Email/Password**
4. Toggle **Enable** → click **Save**

---

## Step 5 — Create Firestore Database

1. Firebase Console → left sidebar → **Firestore Database**
2. Click **"Create database"**
3. Choose **"Start in test mode"** (we will secure it in Step 8)
4. Choose a Cloud Firestore location (e.g., `asia-south1` for India) → click **Enable**

---

## Step 6 — Enable Firebase Storage

1. Firebase Console → left sidebar → **Storage**
2. Click **"Get started"**
3. Choose **"Start in test mode"** → click **Next**
4. Choose the same location as Firestore → click **Done**

---

## Step 7 — Run the App

Since we use ES Modules (`type="module"`), you **must serve the files via a local server** — not by opening HTML files directly.

### Option A: VS Code Live Server (recommended)
1. Install the [Live Server extension](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer) in VS Code
2. Right-click `index.html` → **"Open with Live Server"**
3. App opens at `http://127.0.0.1:5500`

### Option B: Node.js `http-server`
```bash
npm install -g http-server
cd "d:\Hyperiocal Marketplace\Hyperiocal-secondhand-market"
http-server -p 5500
```
Then open `http://localhost:5500`

### Option C: Python simple server
```bash
cd "d:\Hyperiocal Marketplace\Hyperiocal-secondhand-market"
python -m http.server 5500
```

---

## Step 8 — Apply Security Rules (Production)

### Firestore Rules
Firebase Console → Firestore → **Rules** tab → replace with:

```
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /users/{uid} {
      allow read, write: if request.auth != null && request.auth.uid == uid;
    }
    match /products/{docId} {
      allow read: if true;
      allow create: if request.auth != null;
      allow update, delete: if request.auth != null
                            && request.auth.uid == resource.data.sellerId;
    }
  }
}
```

Click **Publish**.

### Storage Rules
Firebase Console → Storage → **Rules** tab → replace with:

```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    match /products/{allPaths=**} {
      allow read: if true;
      allow write: if request.auth != null
                   && request.resource.size < 5 * 1024 * 1024
                   && request.resource.contentType.matches('image/.*');
    }
  }
}
```

Click **Publish**.

---

## Step 9 — Test the Full Flow

| Test Case | Expected Result |
|---|---|
| Register with email + password | User created in Firebase Auth → redirected to homepage |
| Login with wrong password | Inline error message shown |
| Forgot password | Reset email sent |
| Add a product (when logged in) | Image uploaded to Storage, product saved in Firestore, appears on homepage |
| Browse products | Real-time cards with distance-sorted by GPS location |
| Contact seller | Modal opens with WhatsApp link |
| Edit product (My Listings) | Changes saved to Firestore |
| Delete product (My Listings) | Document + Storage image deleted |
| Visit add-product.html while logged out | Auto-redirected to login.html |

---

## Project File Structure

```
Hyperiocal-secondhand-market/
├── firebase-config.js    ← 🔑 Paste your config here
├── index.html            ← 🏠 Main marketplace (Firestore real-time)
├── login.html            ← 🔐 Firebase Auth login + forgot password
├── register.html         ← 📝 Firebase Auth register + Firestore profile
├── add-product.html      ← 📦 Post item (Firestore + Storage upload)
├── my-products.html      ← ✏️ Edit / delete your own listings
├── presentation.html     ← 🎤 Investor pitch deck (10 slides)
├── style.css             ← 🎨 Shared utility styles
└── SETUP.md              ← 📖 This guide
```

---

## Common Errors & Fixes

| Error | Fix |
|---|---|
| `Firebase: Error (auth/configuration-not-found)` | Your API key or authDomain is wrong in `firebase-config.js` |
| `Missing or insufficient permissions` | Apply Firestore security rules from Step 8, or temporarily use test mode |
| `CORS error` or `Module not allowed` | You must use a local dev server (Step 7) — not `file://` |
| Image upload fails | Enable Firebase Storage and check rules allow `write` |
| `onSnapshot` returns no products | Check Firestore collection name is exactly `products` |
