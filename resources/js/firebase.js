import { initializeApp } from "firebase/app";
import { getMessaging } from "firebase/messaging";

const firebaseConfig = {
    apiKey: "AIzaSyCFhC22GzeDFhg8Y7P8FAZi9kQ6zY54GzM",
    authDomain: "gymz-489420.firebaseapp.com",
    projectId: "gymz-489420",
    storageBucket: "gymz-489420.firebasestorage.app",
    messagingSenderId: "1093509789268",
    appId: "1:1093509789268:web:a48af1b3bd29eac45adfc1",
    measurementId: "G-4XKXQ1VZK1"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Cloud Messaging and get a reference to the service
const messaging = getMessaging(app);

export { app, messaging };
