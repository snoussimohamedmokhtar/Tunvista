document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('.message-input');
    const messagesContainer = document.querySelector('.messages-container');

    // Function to send a message to the server
    function sendMessage(message) {
        // Send an HTTP request to the server with the user message
        // Replace '/process_message' with the appropriate route to handle message processing on the backend
        fetch('/process_message', {
            method: 'POST',
            body: JSON.stringify({ message: message }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Display the response from the server in the chat interface
            displayMessage(data.response, 'response');
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Function to display a message in the chat interface
    function displayMessage(message, type) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type);
        messageElement.textContent = message;
        messagesContainer.appendChild(messageElement);
    }

    // Event listener for sending a message when the user presses Enter
    messageInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            const message = messageInput.value.trim();
            if (message !== '') {
                displayMessage(message, 'user');
                sendMessage(message);
                messageInput.value = '';
            }
        }
    });
});
