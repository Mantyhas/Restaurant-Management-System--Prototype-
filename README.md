# Restaurant Chain Reservation & Menu Management System (Prototype)

A web-based prototype for a restaurant chain that streamlines table reservations and centralized menu administration.  
Customers can register/login, browse a default chain-wide menu (with per-restaurant overrides), and reserve tables by selecting a restaurant, date, and time. Reservations follow a status workflow (**pending / approved / rejected**).  

Managers handle menu management (categories, dishes, per-restaurant visibility) and reservation approvals using a floor/table view. An administrator oversees the entire chain, performs final approval/rejection, and manages access permissions.

## Key Features

### Customer
- Registration and authentication
- Browse menu (default chain menu with restaurant-specific visibility)
- Create table reservations (restaurant + date/time + table)
- Track reservation status: pending / approved / rejected

### Manager (Restaurant Admin)
- Menu management (CRUD for categories and dishes)
- Per-restaurant dish visibility (e.g., limited-time or location-specific items)
- View and approve/reject reservations
- Monitor table availability via floor/table layout

### System Administrator
- Global overview of all chain reservations
- Final approval/rejection of reservations
- User access and permission management

## Tech Stack
- **Backend:** PHP  
- **Database:** MySQL  
- **Frontend:** HTML, CSS, JavaScript  
- **Local environment:** XAMPP (Apache + MySQL)

## Getting Started (Local Setup)
1. Install **XAMPP** and start **Apache** + **MySQL**
2. Import the database:
   - Open phpMyAdmin → create a database → import `database.sql`
3. Configure DB connection in `config.php` (host/user/password/dbname)
4. Place the project folder into `xampp/htdocs/`
5. Open in browser: `http://localhost/<project-folder>/`

## Notes
This project was built as a university prototype to demonstrate role-based reservation workflows and menu administration for a multi-restaurant environment.

