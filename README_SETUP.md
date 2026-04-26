
---

## 🛠️ Things you need to Download & Install

Before ta magsugod, make sure naa na nimo kining tulo ka "ingredients" sa imong PC:

1. **PHP (Version 8.3 or higher)**
   - *What is this?* Mao ni ang **Engine or Brain** sa atong system. Siya ang nag-handle sa tanang logic.
   - [Download PHP here](https://windows.php.net/download/) (Pilia ang "Thread Safe" version).

2. **Composer**
   - *What is this?* Mao ni ang **Package Manager** para sa PHP. Siya ang mo-download sa tanang libraries/tools nga kailangan sa atong app.
   - [Download Composer here](https://getcomposer.org/download/) (Run the `Composer-Setup.exe`).

3. **Node.js (LTS Version)**
   - *What is this?* Mao ni ang nag-atiman sa **Frontend and Design** (Vite).
   - [Download Node.js here](https://nodejs.org/) (Choose the "LTS" version).

---

## 🚀 Let's start the Setup!

Basta naay command, i-copy-paste ra nimo sa imong **Terminal** or **PowerShell**:

### Step 1: Open the Project Folder
Ablihi imong terminal/CMD/PowerShell unya adto sa folder kon asa nimo gi-save ang ECROS.
```powershell
cd "c:\Users\User\Desktop\dane\ecros"
```

### Step 2: Install all Dependencies
I-run kining duha ka commands para ma-download tanang "piesa" sa atong system:
```powershell
# This installs PHP libraries
composer install

# This installs Frontend/Design tools
npm install
```

### Step 3: Setup the Configuration (`.env` file)
Copy the sample settings file para mahimo natong actual configuration:
```powershell
cp .env.example .env
```
Then, i-run ni para sa security key generation:
```powershell
php artisan key:generate
```

### Step 4: Setup the Database (SQLite)
Para ma-create ang database tables and sample data (cars, users, etc.), run this command:
```powershell
php artisan migrate:fresh --seed
```

---

## 🚦 How to Run the App?

Kailangan nimo padaganon ang duha (2) ka terminal windows:

1. **Terminal 1 (Backend):**
   Run the PHP server:
   ```powershell
   php artisan serve
   ```
   *Keep this window open!*

2. **Terminal 2 (Frontend):**
   Open a **NEW terminal window**, adto balik sa folder, and run:
   ```powershell
   npm run dev
   ```

---

## 🏁 Check your work!

Open your browser (Chrome or Edge) and go to:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

**Boom!** Ready na imong ECROS system. 🏎️💨

---

### 💡 Quick Tips:
- Make sure both terminals (`artisan serve` and `npm run dev`) are running at the same time.
- If naay error, double-check if na-install ba nimo ang PHP and Node correctly.
- Happy coding!
