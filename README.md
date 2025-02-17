# Wtyczka Custom Form Review

Wtyczka **Custom Form Review** umożliwia dodanie niestandardowego formularza do przesyłania opinii oraz listy wszystkich zgłoszonych opinii w panelu frontowym (z ograniczeniami dostępu wyłącznie dla administratorów).

## Funkcjonalności

1. **Formularz z polami:** Imię, Nazwisko, E-mail, Temat, Wiadomość.
2. **Dostęp dla wszystkich użytkowników:**  
   - Użytkownicy zalogowani mają automatycznie uzupełnione pola (imię, nazwisko, email).  
   - Niezalogowani mogą wypełnić je ręcznie.
3. **Obsługa AJAX:**  
   - Formularz przesyłany jest asynchronicznie do serwera, bez przeładowania strony.  
   - Po udanym przesłaniu formularza wyświetlany jest komunikat podziękowania.  
   - Lista opinii oraz szczegóły wybranego wpisu (dla administratorów) także korzystają z AJAX.
4. **Niestandardowa tabela w bazie danych:**  
   - Podczas aktywacji wtyczki tworzona jest tabela (o ile nie istnieje), w której zapisywane są wpisy.  
   - Dane te nie trafiają do `wp_posts`, lecz do osobnej tabeli, dzięki czemu nie zaburzają standardowych struktur WP.
5. **Lista opinii (tylko dla administratorów):**  
   - Drugi blok Gutenberg wyświetla listę opinii (imię, nazwisko, email, temat), paginowaną po 10 wpisów na stronę.  
   - Po kliknięciu w wybrany wpis szczegóły pobierane są asynchronicznie i wyświetlane pod listą.  
   - Niezalogowani lub użytkownicy bez uprawnień administratora otrzymują komunikat „Nie masz uprawnień do przeglądania zawartości tej strony.”

## Wymagania

- **WordPress** w wersji co najmniej 5.8 (ze wsparciem dla edytora blokowego Gutenberg).
- **PHP** w wersji **7.4** lub wyższej.
- Uprawnienia do tworzenia tabel w bazie danych (podczas aktywacji wtyczki).

## Instalacja

1. Pobierz lub sklonuj repozytorium do folderu `wp-content/plugins/`.
2. Zaloguj się do panelu WordPress i przejdź do sekcji **Wtyczki**.
3. Znajdź **Wtyczka Opinie (Feedback Plugin)** i kliknij **Aktywuj**.
4. Podczas aktywacji wtyczka utworzy niestandardową tabelę w bazie danych (o ile nie istnieje).
5. Dodaj blok **Formularz opinii** do wybranej strony w edytorze blokowym.
6. Dodaj blok **Lista opinii** (jeśli chcesz wyświetlać zgłoszenia adminom).

## Jak używać

### Formularz
1. W edytorze Gutenberg wybierz blok „Formularz opinii” (nazwa może się różnić w zależności od implementacji).
2. Zapisz/Opublikuj stronę z tym blokiem.
3. Użytkownicy mogą wypełnić formularz i przesłać opinię asynchronicznie.

### Lista opinii
1. Dodaj blok „Lista opinii” na stronie, która ma być dostępna tylko dla administratorów.
2. Zaloguj się jako administrator i przejdź do tej strony, aby zobaczyć listę wszystkich opinii.
3. Każdy wpis można rozwinąć, by zobaczyć pełne dane (pobierane AJAX-em).
4. Jeśli odwiedzisz tę stronę jako niezalogowany lub nie będąc administratorem, zobaczysz komunikat o braku uprawnień.

## Tłumaczenia

Wtyczka jest przystosowana do tłumaczeń (funkcje `__()` i `_e()` oraz plik `.pot`). Dodaj swoje tłumaczenia do folderu `languages` (lub innego, w zależności od struktury).

## Rozwój

- **Standard kodowania**: Wtyczka stosuje standardy kodowania WordPress (WP Coding Standards).
- **Kompilacja CSS/JS**: Jeśli korzystasz z narzędzi do transpilacji, dołącz oryginalne pliki źródłowe w repozytorium.

## Autor

- **Imię i nazwisko**: Łukasz Lasota
- **Kontakt**: [lukaszlasota89r@gmail.com](mailto:lukaszlasota89r@gmail.com)

## Licencja

Projekt jest licencjonowany na warunkach [GPLv2 lub nowszej](https://www.gnu.org/licenses/gpl-2.0.html).
