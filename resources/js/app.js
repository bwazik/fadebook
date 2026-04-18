import "./bootstrap";
import { messaging } from "./firebase";
import { getToken } from "firebase/messaging";

window.requestFCMToken = async () => {
    try {
        const permission = await Notification.requestPermission();
        if (permission === "granted") {
            // Use the already-registered SW from the layout — do NOT register again.
            // Re-registering during a Livewire navigation throws InvalidStateError.
            const registration = await navigator.serviceWorker.ready;

            const currentToken = await getToken(messaging, {
                vapidKey:
                    "BMTSUTpn_bU5EcscMRFo3bBLGCFNzUyVlF1h1tnAKDlU-IaKvhVVmkNYelAvnf9VKbhgZ_7yV5gpKBBxSKx93a0",
                serviceWorkerRegistration: registration,
            });

            if (currentToken) {
                console.log("FCM Token:", currentToken);

                // Send token to our Laravel Backend
                try {
                    await axios.post("/fcm-token", {
                        token: currentToken,
                    });
                    console.log(
                        "Token synchronized with backend successfully.",
                    );
                    return true;
                } catch (backendError) {
                    console.error(
                        "Failed to sync token with backend.",
                        backendError,
                    );
                    return false;
                }
            } else {
                console.log("No registration token available.");
                return false;
            }
        } else {
            console.error("Notification permission denied.");
            return false;
        }
    } catch (error) {
        console.error("An error occurred while retrieving token:", error);
        return false;
    }
};
