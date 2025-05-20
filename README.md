# 🖥️ Web prodavnica IT opreme

Ovo je školski PHP projekat – troslojna web aplikacija koja simulira rad prodavnice IT opreme. Korisnici mogu da pregledaju i naručuju proizvode, dok administratori imaju mogućnost upravljanja proizvodima.

---

## Funkcionalnosti

- Registracija i prijava korisnika
- Dve korisničke uloge: **Administrator** i **Korisnik**
- Korpa i pregled porudžbina
- Administrator može:
  - Dodavati nove proizvode
  - Menjati postojeće proizvode
  - Brisati proizvode
  - Pregledati porudžbine
- Dinamički sadržaj (vreme porudžbine, alert poruke...)

---

## 🛠️ Tehnologije

- PHP 8.3
- MySQL 8.0
- HTML / CSS / Bootstrap 5
- JavaScript (za alert poruke)
- Docker i Docker Compose
- Apache web server

---

## 🔐 Admin kredencijali

- **Korisničko ime**: `admin`  
- **Lozinka**: `admin`  
> Lozinka je sačuvana pomoću `password_hash()` u bazi. Automatski se kreira admin nalog prilikom prvog pokretanja.

---

## 📁 Struktura projekta

```
/
├── assets/         # CSS, JS i slike
├── config/         # Konfiguracija baze
├── controllers/    # Kontroleri (AuthController, ProductController)
├── models/         # Modeli za rad sa podacima
├── views/          # View fajlovi
│   ├── admin/      # Admin interfejs
│   ├── auth/       # Login i registracija
│   ├── layout/     # Zajednički elementi (header, footer)
│   └── user/       # Korisnički interfejs
├── Dockerfile      # Konfiguracija Docker kontejnera
├── docker-compose.yaml # Docker Compose konfiguracija
├── index.php       # Glavna ruta
├── init.php        # Inicijalizacija aplikacije
└── setup.php       # Kreiranje baze i tabela
```

---

## ⚙️ Pokretanje sa Docker-om

1. Kloniraj repozitorijum:
   ```bash
   git clone https://github.com/tvoj-username/pc-shop.git
   cd pc-shop
   ```

2. Pokreni Docker kontejnere:
   ```bash
   docker-compose up -d
   ```

3. Otvori u browseru:
   ```
   http://localhost
   ```
   
4. Aplikacija automatski kreira bazu podataka i admin korisnika.

## ⚙️ Pokretanje bez Docker-a (XAMPP)

1. Kloniraj repozitorijum:
   ```bash
   git clone https://github.com/tvoj-username/pc-shop.git
   ```

2. Ubaci projekat u htdocs (ako koristiš XAMPP).

3. Pokreni MySQL i Apache iz XAMPP kontrole.

4. Otvori u browseru:
   ```
   http://localhost/pc-shop/
   ```
   
5. Alternativno, možeš direktno pristupiti setup.php za inicijalizaciju baze:
   ```
   http://localhost/pc-shop/setup.php
   ```