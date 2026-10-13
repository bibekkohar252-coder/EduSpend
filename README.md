# EduSpend 📊

> **An Agile-Driven Student Expense & Financial Management Platform**

EduSpend is a modular web application engineered to solve student budgeting challenges by tracking income, monitoring multi-category expenses, and managing incremental savings goals. This project was developed following strict Agile/Scrum engineering workflows.

---

## 🛠️ My Role: Scrum Master & Core Infrastructure Lead
As the team's **Scrum Master**, I owned project coordination and execution velocity while contributing heavily to the core backend architecture:
*   **Agile Governance:** Managed the product backlog, sprint prioritization frameworks, and team burn-down tracking entirely within **Jira**.
*   **Architecture & Core Routing:** Engineered the core modular structures for transaction persistence, multi-user role isolation (`includes/auth.php`), and automated schema deployments (`eduspend_db.sql`).
*   **Quality Assurance & Testing:** Formulated and deployed explicit server-side unit and integration testing suites (`test_amount.php`, `test_isrecurring.php`) to validate state parameters and prevent pipeline regression.

---

## 🚀 Technical Stack
*   **Backend:** PHP (Modular Backend Routing / PDO Prepared Statements)
*   **Frontend:** HTML5, CSS3, JavaScript (Asynchronous DOM updates)
*   **Database:** MySQL (Relational Schema Layer / Indexed Constraint Optimization)
*   **Testing Suites:** Native PHP Unit Verification Suites
*   **Project Management:** Jira (Agile/Scrum Blueprinting), Git/GitHub Version Control

---

## 🧠 Future Roadmap: Level 6 AI Implementation (Ongoing)
Leveraging my current 3rd-year **Big Data and Machine Learning** curriculum, I am actively architecting a predictive microservice wrapper for EduSpend:
*   **Predictive Financial Runway Engine:** Integrating a Python-based regression service (`FastAPI` + `Scikit-Learn`) to model historical spending vectors (`category_id`, `amount`, `expense_date`) and accurately forecast 30-day student budget limits.
*   **Microservices Decoupling:** Migrating data pipelines into independent container networks via **Docker** to completely decouple the legacy web frontend from automated machine learning scoring tasks.

---

## ⚙️ Local Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com
   ```
2. **Database Setup:** Import the initialization blueprints located inside `eduspend_db.sql` into your local MySQL schema configuration runner.
3. **Configure Environment:** Enforce valid backend path bindings within `includes/config.php`.
4. **Deploy:** Serve via a local Apache instance (e.g., XAMPP/MAMP environment) and navigate to `index.php`.
