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

- PHP (proceduralno i delimično MVC pristup)
- MySQL (ili MariaDB)
- HTML / CSS / Bootstrap
- JavaScript (za alert poruke)
- XAMPP (lokalni server)

---

## 🔐 Admin kredencijali

- **Korisničko ime**: `admin`  
- **Lozinka**: `admin123`  
> Lozinka je sačuvana pomoću `password_hash()` u bazi.

---

## ⚙️ Pokretanje

1. Kloniraj repozitorijum:
   ```bash
   git clone https://github.com/tvoj-username/pc-shop.git
2. Ubaci projekat u htdocs (ako koristiš XAMPP).

3. Pokreni MySQL i Apache iz XAMPP kontrole.

4. Kreiraj bazu pomoću db.sql fajla (ako postoji) ili izvrši ručno SQL skripte.

5. Otvori u browseru
[text](http://localhost/pc-shop/)