/**
 * Content Form Module - Handles tick/untick functionality for presenters
 * Integrates with the ContentForm model to track presenter interactions
 * Real-time tracking of all presenter reading actions
 */

class ContentFormManager {
    constructor() {
        this.csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";
        this.contentFormCache = new Map(); // Cache for ContentForm IDs
        this.init();
    }

    init() {
        console.log("🎯 ContentFormManager initialized");
        this.attachEventListeners();
    }

    /**
     * Attach event listeners to reading buttons
     * Intercepts clicks on .reading-btn elements
     */
    attachEventListeners() {
        document.addEventListener("click", async (e) => {
            const button = e.target.closest(".reading-btn");
            if (!button) return;

            // Prevent default behavior
            e.preventDefault();
            e.stopPropagation();

            // Handle the reading button click
            await this.handleReadingButtonClick(button);
        });

        console.log("✅ Event listeners attached to reading buttons");
    }

    /**
     * Handle reading button click
     * Determines if it's a tick or untick action and sends to backend
     */
    async handleReadingButtonClick(button) {
        try {
            const stickyNote = button.closest(".sticky-note");
            if (!stickyNote) {
                console.error("❌ Sticky note not found");
                return;
            }

            const contentType = stickyNote.dataset.type;
            const contentId = parseInt(stickyNote.dataset.id);
            const timeSlot = stickyNote.dataset.timeSlot;
            const readingNumber = parseInt(button.dataset.readingNumber);

            console.log(`📍 Reading button clicked:`, {
                contentType,
                contentId,
                timeSlot,
                readingNumber,
                isCurrentlyRead: button.classList.contains("read"),
            });

            // Get or create ContentForm
            const contentFormId = await this.getOrCreateContentForm(
                contentType,
                contentId
            );
            if (!contentFormId) {
                console.error("❌ Failed to get or create ContentForm");
                alert(
                    "Error: Could not find content form. Please refresh the page."
                );
                return;
            }

            // Determine action based on current state
            const isCurrentlyRead = button.classList.contains("read");
            const action = isCurrentlyRead ? "untick" : "tick";

            console.log(`🔄 Sending ${action} request...`);

            // Send tick/untick request
            const result = await this.sendTickUntickRequest(
                contentFormId,
                timeSlot,
                action,
                readingNumber
            );

            if (result && result.success) {
                // Update button UI immediately
                this.updateButtonUI(button, action, result);

                // Show success message
                console.log(`✅ ${action} successful!`, result);
                this.showNotification(
                    `Reading ${
                        action === "tick" ? "recorded" : "removed"
                    } successfully!`,
                    "success"
                );
            } else {
                console.error(`❌ ${action} failed:`, result?.message);
                this.showNotification(
                    `Error: ${result?.message || "Unknown error"}`,
                    "error"
                );
            }
        } catch (error) {
            console.error("❌ Error handling reading button click:", error);
            this.showNotification(`Error: ${error.message}`, "error");
        }
    }

    /**
     * Get or create ContentForm for the given content
     * Uses cache to avoid repeated API calls
     */
    async getOrCreateContentForm(contentType, contentId) {
        try {
            const cacheKey = `${contentType}_${contentId}`;

            // Check cache first
            if (this.contentFormCache.has(cacheKey)) {
                console.log(`📦 Using cached ContentForm ID for ${cacheKey}`);
                return this.contentFormCache.get(cacheKey);
            }

            // Fetch all presenter forms
            const response = await fetch("/presenter/content-forms", {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": this.csrfToken,
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success || !data.data) {
                throw new Error("Invalid response from server");
            }

            // Find ContentForm for this content
            const existingForm = data.data.find(
                (form) =>
                    form.content_type === contentType &&
                    form.content_id === contentId
            );

            if (existingForm) {
                // Cache it
                this.contentFormCache.set(cacheKey, existingForm.id);
                console.log(`✅ Found ContentForm ID: ${existingForm.id}`);
                return existingForm.id;
            }

            console.warn(
                `⚠️ ContentForm not found for ${contentType} ${contentId}`
            );
            return null;
        } catch (error) {
            console.error("❌ Error getting ContentForm:", error);
            return null;
        }
    }

    /**
     * Send tick/untick request to backend
     * Records the action in real-time
     */
    async sendTickUntickRequest(
        contentFormId,
        timeSlot,
        action,
        readingNumber = null
    ) {
        try {
            const endpoint =
                action === "tick"
                    ? "/presenter/content-form/tick"
                    : "/presenter/content-form/untick";

            console.log(`📤 Sending ${action} request to ${endpoint}`, {
                contentFormId,
                timeSlot,
                readingNumber,
            });

            const requestBody = {
                content_form_id: contentFormId,
                time_slot: timeSlot,
            };

            // Include reading_number if provided
            if (readingNumber) {
                requestBody.reading_number = readingNumber;
            }

            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify(requestBody),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            console.log(`📥 Response received:`, data);

            return data;
        } catch (error) {
            console.error(`❌ Error during ${action}:`, error);
            throw error;
        }
    }

    /**
     * Update button UI after successful action
     */
    updateButtonUI(button, action, result) {
        if (action === "tick") {
            // Add read class
            button.classList.add("read");
            button.classList.remove("unread");
        } else {
            // Remove read class
            button.classList.remove("read");
            button.classList.add("unread");
        }

        // Update button appearance
        button.style.opacity = "1";
        button.style.transform = "scale(1)";

        console.log(`🎨 Button UI updated for ${action}`);
    }

    /**
     * Show notification to user
     */
    showNotification(message, type = "info") {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === "success"
                ? "bg-green-500"
                : type === "error"
                ? "bg-red-500"
                : "bg-blue-500"
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    /**
     * Get ContentForm details
     */
    async getContentFormDetails(contentFormId) {
        try {
            const response = await fetch(
                `/presenter/content-form/${contentFormId}`,
                {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": this.csrfToken,
                        Accept: "application/json",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error("❌ Error getting ContentForm details:", error);
            return null;
        }
    }

    /**
     * Get all ContentForms for current presenter
     */
    async getPresenterContentForms() {
        try {
            const response = await fetch("/presenter/content-forms", {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": this.csrfToken,
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error("❌ Error getting presenter ContentForms:", error);
            return null;
        }
    }
}

// Initialize ContentFormManager when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        window.contentFormManager = new ContentFormManager();
    });
} else {
    // DOM is already loaded
    window.contentFormManager = new ContentFormManager();
}
