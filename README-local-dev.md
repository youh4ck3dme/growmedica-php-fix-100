# Lokálny vývojový systém (Docker) pre GrowMedica PHP e-shop

Tento súbor popisuje inštaláciu a spustenie legacy PHP e-shopu v izolovanom lokálnom prostredí pomocou Dockeru.

## Prerekvizity

- **Docker Desktop** nainštalovaný a spustený na vašom Macu.

---

## Rýchly štart

1. **Vytvorenie lokálnej konfigurácie**
   V projekte sa nachádza predkonfigurovaný súbor `.env.local`, ktorý sa automaticky načíta do Docker kontajnera. Ak by ste ho potrebovali v budúcnosti upraviť, môžete si vytvoriť vlastný podľa vzoru `.env.local.example`.

2. **Spustenie Docker kontajnerov**
   Spustite zostavenie a zapnutie služieb (Web server Apache, MariaDB, Adminer):
   ```bash
   docker compose up -d --build
   ```

3. **Kontrola logov (ak je potrebná)**
   V prípade, že chcete sledovať chyby alebo správanie Apache/PHP servera:
   ```bash
   docker compose logs -f app
   ```

---

## Import Databázy

Pre plnú funkčnosť e-shopu je potrebné importovať databázový dump z produkcie (stiahnutý z hosting administrácie SIXNET).

### A) Import produkčných dát (odporúčaný):
1. Uložte váš stiahnutý SQL dump z produkcie pod názvom `dump.sql` do koreňového adresára tohto projektu.
2. Spustite príkaz pre import do bežiaceho databázového kontajnera:
   ```bash
   docker exec -i c1growmedical-db mysql -u c1growmedical -plocalpassword123 c1growmedical < dump.sql
   ```

### B) Import testovacej štruktúry (voliteľný):
Ak zatiaľ nemáte produkčný SQL dump, môžete importovať čistú štruktúru s ukážkovými dátami z roku 2013, ktorá sa nachádza v projekte:
```bash
docker exec -i c1growmedical-db mysql -u c1growmedical -plocalpassword123 c1growmedical < shared/mod_sql/eshop/sql.sql
```

---

## Lokálne URL adresy

Po úspešnom spustení a importe databázy môžete navštíviť:

- **E-shop (Web):** [http://localhost:8080](http://localhost:8080)
- **Adminer (Správa DB):** [http://localhost:8081](http://localhost:8081)
  * Prihlasovacie údaje do Adminera:
    * **System:** `MySQL`
    * **Server:** `db`
    * **Username:** `c1growmedical`
    * **Password:** `localpassword123`
    * **Database:** `c1growmedical`

---

## Vypnutie prostredia

Keď skončíte s lokálnym vývojom, prostredie vypnete príkazom:
```bash
docker compose down
```
*(Dáta v databáze zostanú zachované vďaka Docker volume `db_data`)*
