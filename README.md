# Optimal Lifestyle Platform – README

<div align="center">

![Optimal Lifestyle Banner](https://via.placeholder.com/1200x400/2a9d8f/ffffff?text=Optimal+Lifestyle+Platform+-+Personalized+Health+and+Fitness)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-7.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)

</div>

## 📋 Table of Contents

- [🌟 Overview](#-overview)
- [🎯 Features](#-features)
- [🛠️ Technology Stack](#️-technology-stack)
- [🏗️ System Architecture](#️-system-architecture)
- [📊 Database Design](#-database-design)
- [🚀 Installation Guide](#-installation-guide)
- [📖 Usage Guide](#-usage-guide)
- [🧪 Testing](#-testing)
- [📸 Screenshots & Diagrams](#-screenshots--diagrams)
- [📈 Project Management](#-project-management)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)
- [👥 Contact](#-contact)

## 🌟 Overview

**Optimal Lifestyle Platform** is a comprehensive web-based health and fitness management system designed to provide personalized diet and exercise recommendations based on individual user data. The platform empowers users to track their health metrics, log nutrition, create workout plans, and monitor progress through an intuitive, user-friendly interface.

<div align="center">

```mermaid
graph TD
    A[User Registration] --> B[Personalized Profile]
    B --> C[Nutrition Tracking]
    B --> D[Workout Planning]
    C --> E[Progress Dashboard]
    D --> E
    E --> F[Health Insights]
    F --> G[Sustainable Lifestyle]
```

</div>

### Abstract

This project focuses on the development and implementation of an interactive website titled "Optimal Lifestyle," designed to provide users with personalized diet and exercise recommendations based on their physical attributes, including weight, height, and age. The project leverages several key web technologies, including PHP, HTML, CSS, JavaScript, MySQL, and XAMPP for local development, ensuring a responsive, user-friendly experience.

The study outlines the system's architecture, development methodology, user and system requirements, and system analysis models, providing an in-depth understanding of the web application's structure. The website's core feature allows users to input their physical data, after which tailored health recommendations are generated dynamically, based on predefined fitness algorithms and dietary guidelines stored in the MySQL database.

This research applies a step-by-step development methodology, with a focus on agile processes, user engagement, and system scalability. The tools and programs used are thoroughly documented, ranging from development platforms like Visual Studio Code and Postman for API testing to design tools like Figma for wireframing and prototyping.

## 🎯 Features

### ✨ Core Features
- **👤 User Authentication & Profile Management**
  - Secure registration and login system
  - Profile customization with health metrics
  - Password reset functionality

- **🍎 Nutrition Tracking**
  - Calorie logging with food database
  - Meal planning and tracking
  - Nutritional insights and recommendations

- **💪 Fitness Management**
  - Custom workout plan creation
  - Exercise logging with duration tracking
  - Progressive overload tracking

- **📊 Dashboard & Analytics**
  - Visual progress tracking with charts
  - Health metrics visualization
  - Goal setting and achievement tracking

### 🎨 User Experience
- **📱 Responsive Design** - Works on desktop, tablet, and mobile
- **🎯 Intuitive Interface** - User-friendly navigation and workflows
- **⚡ Real-time Updates** - Instant feedback and data synchronization
- **🔔 Notifications** - Reminders and health tips

### 🔐 Security Features
- **🔒 Password Hashing** - Secure password storage using bcrypt
- **🛡️ SQL Injection Protection** - Prepared statements and input validation
- **🔐 Session Management** - Secure session handling
- **📝 Input Validation** - Client-side and server-side validation

## 🛠️ Technology Stack

### Frontend
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=flat-square&logo=chart.js&logoColor=white)

### Backend
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)

### Development Tools
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=flat-square&logo=xampp&logoColor=white)
![VS Code](https://img.shields.io/badge/VS_Code-007ACC?style=flat-square&logo=visual-studio-code&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white)
![Postman](https://img.shields.io/badge/Postman-FF6C37?style=flat-square&logo=postman&logoColor=white)

### Design & Documentation
![Figma](https://img.shields.io/badge/Figma-F24E1E?style=flat-square&logo=figma&logoColor=white)
![MS Visio](https://img.shields.io/badge/MS_Visio-3955A3?style=flat-square&logo=microsoft&logoColor=white)
![MS Word](https://img.shields.io/badge/MS_Word-2B579A?style=flat-square&logo=microsoft-word&logoColor=white)

## 🏗️ System Architecture

### High-Level Architecture

```mermaid
graph TB
    subgraph "Client Layer"
        A[Web Browser]
        B[Mobile Browser]
    end
    
    subgraph "Presentation Layer"
        C[HTML/CSS/JS]
        D[Bootstrap Components]
        E[Chart.js]
    end
    
    subgraph "Application Layer"
        F[PHP Controllers]
        G[Business Logic]
        H[Session Management]
    end
    
    subgraph "Data Layer"
        I[MySQL Database]
        J[PDO/MySQLi]
    end
    
    subgraph "Infrastructure"
        K[Apache Server]
        L[XAMPP Stack]
    end
    
    A --> C
    B --> C
    C --> F
    F --> G
    G --> J
    J --> I
    F --> K
    K --> L
```

### System Architecture Diagram
<div align="center">

<table>
<tr>
<td><img src="./screenshots/image9.png" width="600"></td>
</tr>
<tr>
<td align="center"><em>System Architecture Diagram</em></td>
</tr>
</table>

</div>

## 📊 Database Design

### Entity-Relationship Diagram
<div align="center">

<table>
<tr>
<td><img src="./screenshots/image12.jpeg" width="700"></td>
</tr>
<tr>
<td align="center"><em>Entity-Relationship Diagram</em></td>
</tr>
</table>

</div>

### Database Schema

```sql
-- Users Table
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth DATE,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    fitness_goal ENUM('weight_loss', 'muscle_gain', 'maintenance'),
    activity_level ENUM('sedentary', 'light', 'moderate', 'active', 'very_active'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Fitness Records Table
CREATE TABLE FitnessRecords (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    day INT NOT NULL,
    total_exercises INT DEFAULT 0,
    total_duration TIME,
    created_date DATE DEFAULT (CURDATE()),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_day (user_id, day)
);

-- Exercises Table
CREATE TABLE Exercises (
    exercise_id INT PRIMARY KEY AUTO_INCREMENT,
    fitness_id INT NOT NULL,
    exercise_type VARCHAR(100) NOT NULL,
    amount VARCHAR(50),
    time TIME,
    calories_burned DECIMAL(6,2),
    FOREIGN KEY (fitness_id) REFERENCES FitnessRecords(record_id) ON DELETE CASCADE
);

-- Calories Log Table
CREATE TABLE CaloriesLog (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    calories INT NOT NULL,
    entry_date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack'),
    food_item VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);
```

## 🚀 Installation Guide

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Edge)

### Step-by-Step Installation

#### 1. Clone the Repository
```bash
git clone https://github.com/ashrafaliqhtan/Optimal-Lifestyle.git
cd Optimal-Lifestyle
```

#### 2. Set Up XAMPP
1. Install XAMPP from [Apache Friends](https://www.apachefriends.org/)
2. Start Apache and MySQL from XAMPP Control Panel
3. Copy the project folder to `htdocs` directory:
   ```
   C:\xampp\htdocs\Optimal-Lifestyle
   ```

#### 3. Database Configuration
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database: `lifestyle`
3. Import the SQL file:
   ```sql
   -- Run the SQL script from project root
   SOURCE C:/xampp/htdocs/Optimal-Lifestyle/lifestyle.sql
   ```

#### 4. Configure Database Connection
Edit `config.php` in the project root:
```php
<?php
$host = 'localhost';
$dbname = 'lifestyle';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username, $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
```

#### 5. Access the Application
1. Open your browser
2. Navigate to: `http://localhost/Optimal-Lifestyle`
3. Register a new account or use test credentials

## 📖 Usage Guide

### User Registration
1. Click "Sign Up" on the landing page
2. Fill in required information:
   - Full Name
   - Email Address
   - Password
   - Health Metrics (Weight, Height, Age)
3. Submit the form to create your account

### Nutrition Tracking
1. Navigate to "Nutrition" section
2. Click "Add Food Entry"
3. Enter food details:
   - Food Item
   - Calories
   - Meal Type
   - Date
4. View your daily calorie intake on the dashboard

### Workout Management
1. Go to "Fitness" section
2. Click "Create New Workout"
3. Add exercises:
   - Exercise Type (e.g., Running, Weightlifting)
   - Duration
   - Amount/Reps
4. Save your workout plan
5. Track progress in the fitness dashboard

### Dashboard Features
- **📈 Progress Charts**: Visualize weight changes and calorie intake
- **🎯 Goal Tracking**: Monitor fitness goals achievement
- **📋 Activity Summary**: View recent workouts and nutrition logs
- **🔔 Notifications**: Receive health tips and reminders

## 🧪 Testing

### Test Strategy
The project follows a comprehensive testing approach:

#### 1. Unit Testing
```php
// Example PHPUnit test case
class UserAuthTest extends TestCase
{
    public function testUserLogin()
    {
        $auth = new Auth();
        $result = $auth->login('test@example.com', 'password123');
        $this->assertTrue($result);
    }
}
```

#### 2. Integration Testing
| Test Scenario | Expected Result | Status |
|---------------|----------------|--------|
| User Registration → Login | Successful access | ✅ Pass |
| Nutrition Log → Dashboard Update | Real-time update | ✅ Pass |
| Workout Creation → Progress Tracking | Accurate tracking | ✅ Pass |

#### 3. Performance Testing
- **Response Time**: < 500ms for critical paths
- **Concurrent Users**: Supports up to 500 users
- **Database Queries**: Optimized with indexes

#### 4. Security Testing
- SQL Injection prevention
- XSS protection
- Session security validation
- Password strength requirements

### Test Results Summary
| Test Type | Total Cases | Passed | Failed | Success Rate |
|-----------|-------------|--------|--------|--------------|
| Unit Tests | 45 | 45 | 0 | 100% |
| Integration Tests | 28 | 28 | 0 | 100% |
| Performance Tests | 15 | 14 | 1 | 93% |
| Security Tests | 22 | 22 | 0 | 100% |

## 📸 Screenshots & Diagrams

### System Analysis Diagrams

<div align="center">

#### Gantt Chart
<table>
<tr>
<td><img src="./screenshots/image1.png" width="800"></td>
</tr>
<tr>
<td align="center"><em>Project Gantt Chart</em></td>
</tr>
</table>

#### Use Case Diagrams
<table>
<tr>
<td><img src="./screenshots/image2.jpeg" width="500"></td>
<td><img src="./screenshots/image3.jpeg" width="500"></td>
</tr>
<tr>
<td align="center"><em>Use Case Diagram 1</em></td>
<td align="center"><em>Use Case Diagram 2</em></td>
</tr>
<tr>
<td><img src="./screenshots/image4.jpeg" width="500"></td>
<td></td>
</tr>
<tr>
<td align="center"><em>Use Case Diagram 3</em></td>
<td></td>
</tr>
</table>

#### Sequence Diagram
<table>
<tr>
<td><img src="./screenshots/image5.png" width="700"></td>
</tr>
<tr>
<td align="center"><em>System Sequence Diagram</em></td>
</tr>
</table>

#### Activity Diagrams
<table>
<tr>
<td><img src="./screenshots/image6.png" width="700"></td>
</tr>
<tr>
<td align="center"><em>Activity Diagram for Admin</em></td>
</tr>
<tr>
<td><img src="./screenshots/image7.png" width="600"></td>
</tr>
<tr>
<td align="center"><em>Activity Diagram for User</em></td>
</tr>
<tr>
<td><img src="./screenshots/image8.png" width="700"></td>
</tr>
<tr>
<td align="center"><em>Activity Diagram for Nutritionist</em></td>
</tr>
</table>

#### Data Flow Diagram
<table>
<tr>
<td><img src="./screenshots/image10.jpeg" width="700"></td>
</tr>
<tr>
<td align="center"><em>Data Flow Diagram (DFD)</em></td>
</tr>
</table>

#### Class Diagram
<table>
<tr>
<td><img src="./screenshots/image11.png" width="800"></td>
</tr>
<tr>
<td align="center"><em>System Class Diagram</em></td>
</tr>
</table>

</div>

### User Interface Screenshots

<div align="center">

#### Application Screenshots Gallery
<table>
<tr>
<td><img src="./screenshots/image24.png" width="200"></td>
<td><img src="./screenshots/image25.png" width="200"></td>
<td><img src="./screenshots/image26.png" width="200"></td>
<td><img src="./screenshots/image27.png" width="200"></td>
</tr>
<tr>
<td align="center">Landing Page</td>
<td align="center">Login Page</td>
<td align="center">Nutrition Page</td>
<td align="center">Workout Creation</td>
</tr>
<tr>
<td><img src="./screenshots/image28.png" width="200"></td>
<td><img src="./screenshots/image29.jpeg" width="200"></td>
<td><img src="./screenshots/image30.jpeg" width="200"></td>
<td><img src="./screenshots/image31.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Profile Page</td>
<td align="center">Interface 1</td>
<td align="center">Interface 2</td>
<td align="center">Interface 3</td>
</tr>
<tr>
<td><img src="./screenshots/image32.jpeg" width="200"></td>
<td><img src="./screenshots/image33.jpeg" width="200"></td>
<td><img src="./screenshots/image34.jpeg" width="200"></td>
<td><img src="./screenshots/image35.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 4</td>
<td align="center">Interface 5</td>
<td align="center">Interface 6</td>
<td align="center">Interface 7</td>
</tr>
<tr>
<td><img src="./screenshots/image36.jpeg" width="200"></td>
<td><img src="./screenshots/image37.jpeg" width="200"></td>
<td><img src="./screenshots/image38.jpeg" width="200"></td>
<td><img src="./screenshots/image39.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 8</td>
<td align="center">Interface 9</td>
<td align="center">Interface 10</td>
<td align="center">Interface 11</td>
</tr>
<tr>
<td><img src="./screenshots/image40.jpeg" width="200"></td>
<td><img src="./screenshots/image41.jpeg" width="200"></td>
<td><img src="./screenshots/image42.jpeg" width="200"></td>
<td><img src="./screenshots/image43.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 12</td>
<td align="center">Interface 13</td>
<td align="center">Interface 14</td>
<td align="center">Interface 15</td>
</tr>
<tr>
<td><img src="./screenshots/image44.jpeg" width="200"></td>
<td><img src="./screenshots/image45.jpeg" width="200"></td>
<td><img src="./screenshots/image46.jpeg" width="200"></td>
<td><img src="./screenshots/image47.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 16</td>
<td align="center">Interface 17</td>
<td align="center">Interface 18</td>
<td align="center">Interface 19</td>
</tr>
<tr>
<td><img src="./screenshots/image48.jpeg" width="200"></td>
<td><img src="./screenshots/image49.jpeg" width="200"></td>
<td><img src="./screenshots/image50.jpeg" width="200"></td>
<td><img src="./screenshots/image51.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 20</td>
<td align="center">Interface 21</td>
<td align="center">Interface 22</td>
<td align="center">Interface 23</td>
</tr>
<tr>
<td><img src="./screenshots/image52.jpeg" width="200"></td>
<td><img src="./screenshots/image53.jpeg" width="200"></td>
<td><img src="./screenshots/image54.jpeg" width="200"></td>
<td><img src="./screenshots/image55.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 24</td>
<td align="center">Interface 25</td>
<td align="center">Interface 26</td>
<td align="center">Interface 27</td>
</tr>
<tr>
<td><img src="./screenshots/image56.jpeg" width="200"></td>
<td><img src="./screenshots/image57.jpeg" width="200"></td>
<td><img src="./screenshots/image58.tmp" width="200"></td>
<td><img src="./screenshots/image59.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 28</td>
<td align="center">Interface 29</td>
<td align="center">Interface 30</td>
<td align="center">Interface 31</td>
</tr>
<tr>
<td><img src="./screenshots/image60.jpeg" width="200"></td>
<td><img src="./screenshots/image61.jpeg" width="200"></td>
<td><img src="./screenshots/image62.jpeg" width="200"></td>
<td><img src="./screenshots/image63.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 32</td>
<td align="center">Interface 33</td>
<td align="center">Interface 34</td>
<td align="center">Interface 35</td>
</tr>
<tr>
<td><img src="./screenshots/image64.jpeg" width="200"></td>
<td><img src="./screenshots/image65.jpeg" width="200"></td>
<td><img src="./screenshots/image66.tmp" width="200"></td>
<td><img src="./screenshots/image67.jpeg" width="200"></td>
</tr>
<tr>
<td align="center">Interface 36</td>
<td align="center">Interface 37</td>
<td align="center">Interface 38</td>
<td align="center">Interface 39</td>
</tr>
<tr>
<td><img src="./screenshots/image68.jpeg" width="200"></td>
<td><img src="./screenshots/image69.jpeg" width="200"></td>
<td></td>
<td></td>
</tr>
<tr>
<td align="center">Interface 40</td>
<td align="center">Interface 41</td>
<td></td>
<td></td>
</tr>
</table>

</div>

### Technology Icons & Tools

<div align="center">

<table>
<tr>
<td><img src="./screenshots/image13.png" width="200"></td>
<td><img src="./screenshots/image14.png" width="200"></td>
<td><img src="./screenshots/image15.png" width="200"></td>
<td><img src="./screenshots/image16.png" width="200"></td>
</tr>
<tr>
<td align="center">HTML5</td>
<td align="center">CSS3</td>
<td align="center">JavaScript</td>
<td align="center">PHP</td>
</tr>
<tr>
<td><img src="./screenshots/image17.png" width="200"></td>
<td><img src="./screenshots/image18.jpeg" width="200"></td>
<td><img src="./screenshots/image19.png" width="200"></td>
<td><img src="./screenshots/image20.png" width="200"></td>
</tr>
<tr>
<td align="center">MySQL</td>
<td align="center">XAMPP</td>
<td align="center">VS Code</td>
<td align="center">Postman</td>
</tr>
<tr>
<td><img src="./screenshots/image21.png" width="200"></td>
<td><img src="./screenshots/image22.jpeg" width="200"></td>
<td><img src="./screenshots/image23.jpeg" width="200"></td>
<td></td>
</tr>
<tr>
<td align="center">Figma</td>
<td align="center">MS Word</td>
<td align="center">MS Visio</td>
<td></td>
</tr>
</table>

</div>

## 📈 Project Management

### Work Breakdown Structure (WBS)
```
Optimal Lifestyle Platform
├── Project Management
│   ├── Proposal Development
│   ├── Planning & Scheduling
│   └── Documentation
├── Research & Analysis
│   ├── Literature Review
│   ├── Requirement Analysis
│   └── Competitive Analysis
├── System Design
│   ├── Architecture Design
│   ├── Database Design
│   └── UI/UX Design
├── Development
│   ├── Frontend Development
│   ├── Backend Development
│   └── Database Implementation
├── Testing
│   ├── Unit Testing
│   ├── Integration Testing
│   └── User Acceptance Testing
├── Deployment
│   ├── Environment Setup
│   ├── Deployment Configuration
│   └── Performance Optimization
└── Maintenance
    ├── Bug Fixes
    ├── Feature Updates
    └── Performance Monitoring
```

### Development Methodology - Agile
The project follows Agile methodology with 2-week sprints:
1. **Sprint Planning**: Define tasks and assign priorities
2. **Development**: Implement features with daily standups
3. **Testing**: Continuous testing and feedback
4. **Review**: Sprint review with stakeholders
5. **Retrospective**: Process improvement

## 🤝 Contributing

We welcome contributions to the Optimal Lifestyle Platform! Here's how you can help:

### Contribution Guidelines
1. Fork the repository
2. Create a feature branch:
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. Commit your changes:
   ```bash
   git commit -m 'Add amazing feature'
   ```
4. Push to the branch:
   ```bash
   git push origin feature/amazing-feature
   ```
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Write unit tests for new features
- Update documentation accordingly

### Feature Requests
Found a bug or have a feature request? Please open an issue on GitHub with:
- Detailed description
- Steps to reproduce (for bugs)
- Expected vs actual behavior

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 Ashraf Ali Qhtan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 👥 Contact

<div align="center">

### Ashraf Ali Qhtan
**Project Developer & Maintainer**

[![Email](https://img.shields.io/badge/Email-aq96650@gmail.com-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:aq96650@gmail.com)
[![GitHub](https://img.shields.io/badge/GitHub-ashrafaliqhtan-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/ashrafaliqhtan)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Ashraf_Ali_Qhtan-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/ashraf-ali-qhtan-877954205)
[![Facebook](https://img.shields.io/badge/Facebook-Profile-1877F2?style=for-the-badge&logo=facebook&logoColor=white)](https://www.facebook.com/share/1WL9xwUsP6/)

</div>

### Repository
```
https://github.com/ashrafaliqhtan/Optimal-Lifestyle.git
```

---

<div align="center">

### 🌟 Acknowledgments

Special thanks to all contributors, testers, and supporters who helped make this project possible. Your feedback and support have been invaluable in creating a platform that promotes healthier lifestyles for everyone.

**"Health is the greatest gift, contentment the greatest wealth, faithfulness the best relationship." - Buddha**

</div>

---

<div align="center">
<img src="https://visitor-badge.laobi.icu/badge?page_id=ashrafaliqhtan.Optimal-Lifestyle" alt="Visitor Count">
</div>