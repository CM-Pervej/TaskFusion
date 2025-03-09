# TaskFusion

TaskFusion is a robust task management system designed to streamline workflows using modern web technologies. It incorporates JSON Web Signature (JWS) for secure authentication, a comprehensive log system, unit testing, rate limiting, and the round-robin technique for task allocation.

## Features

- **JWS Authentication**: Ensures secure and tamper-proof authentication.
- **Task Management**: Create, assign, track, and manage tasks efficiently.
- **Log System**: Tracks system events for better debugging and auditing.
- **Unit Testing**: Implements rigorous testing to ensure reliability.
- **Rate Limiting**: Prevents system overload by limiting requests.
- **Round-Robin Task Allocation**: Evenly distributes tasks among team members.

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript (Tailwind CSS & DaisyUI for UI styling)
- **Backend**: PHP (with Laravel framework)
- **Database**: MySQL
- **Security**: JSON Web Signature (JWS) for authentication
- **Libraries**: JSPDF for document generation

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/yourusername/taskfusion.git
   cd taskfusion
   ```

2. Install dependencies:
   ```sh
   composer install
   npm install
   ```

3. Configure the environment:
   - Copy the `.env.example` file to `.env`
   - Update database credentials

4. Run migrations:
   ```sh
   php artisan migrate
   ```

5. Start the development server:
   ```sh
   php artisan serve
   npm run dev
   ```

## Usage

- Register or log in using JWS authentication.
- Create new tasks and assign them to team members.
- Track progress using the dashboard.
- View logs for system activities.
- Download reports in PDF format.

## Contribution

Contributions are welcome! Feel free to submit issues and pull requests.

## License

This project is licensed under the MIT License.

---

Enjoy using **TaskFusion** and enhance your task management experience!

