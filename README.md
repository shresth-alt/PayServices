# PayServices

PayServices is an on-demand platform that connects users with technicians specializing in electrical appliance repairs. The platform offers real-time geolocation, secure payments, and a user-friendly interface. Built with React Native, Node.js, and MongoDB, PayServices is designed to be scalable for future service expansions, ensuring convenience and reliability for its users.

## Features

- **Real-Time Geolocation:** Track technicians in real-time to monitor their arrival status.
- **Secure Payments:** Integrated payment gateway for safe and efficient transactions.
- **User-Friendly Interface:** Intuitive design for seamless navigation and service booking.
- **Scalability:** Architecture supports easy addition of new services and features.

## Tech Stack

- **Frontend:** React Native
- **Backend:** Node.js
- **Database:** MongoDB

## Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/shresth-alt/PayServices.git
   ```
2. **Navigate to the Project Directory:**
   ```bash
   cd PayServices
   ```
3. **Install Dependencies:**
   - For the backend:
     ```bash
     cd backend
     npm install
     ```
   - For the frontend:
     ```bash
     cd ../frontend
     npm install
     ```
4. **Set Up Environment Variables:**
   - Create a `.env` file in both the `backend` and `frontend` directories.
   - Add the necessary environment variables as specified in `.env.example` files.

5. **Run the Application:**
   - Start the backend server:
     ```bash
     cd backend
     npm start
     ```
   - Start the frontend application:
     ```bash
     cd ../frontend
     npm start
     ```

## Usage

- **Booking a Service:**
  - Open the app and sign up or log in.
  - Browse available services and select the desired repair service.
  - Choose a convenient time slot and confirm the booking.

- **Tracking Technician:**
  - After booking, view the assigned technician's real-time location on the map.
  - Receive notifications upon technician arrival.

- **Making Payments:**
  - Use the integrated payment gateway to pay securely after service completion.
  - View transaction history in your profile.

## Contributing

We welcome contributions to enhance PayServices. To contribute:

1. **Fork the Repository.**
2. **Create a New Branch:**
   ```bash
   git checkout -b feature/YourFeatureName
   ```
3. **Make Your Changes and Commit:**
   ```bash
   git commit -m 'Add some feature'
   ```
4. **Push to the Branch:**
   ```bash
   git push origin feature/YourFeatureName
   ```
5. **Open a Pull Request.**


