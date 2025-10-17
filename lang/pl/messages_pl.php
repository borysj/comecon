<?php

define('EXITMSG_BADCOMMENTCAPTCHA', "Czy nie jesteś przypadkiem robotem?");
define('EXITMSG_WRONGPASSWORD', "Komentarz nie został dodany.<br>
    Użyłeś w niewłaściwy sposób zarezerwowanego podpisu.");
define('EXITMSG_ERRORURL', "Nastąpił błąd podczas odczytywania adresu strony,<br>
    gdzie ma zostać umieszczony komentarz.");
define('EXITMSG_DUPLICATE', "Wykryto duplikat komentarza.<br>Prawdopodobnie nacisnąłeś coś
    kilka razy pod rząd<br>albo spróbowałeś odświeżyć potwierdzenie dodania komentarza.");
define('EXITMSG_ERRORSAVINGCOMMENT', "Wystąpił błąd podczas dodawania komentarza.<br>
    Wróć do poprzedniej strony i spróbuj ponownie.<br>Jeżeli błąd będzie nieustępliwy, daj mi znać na {$settings['email']['blogContactMail']}");
define('EXITMSG_ERRORRUNNINGCOMMENTSCRIPT', "Błąd aktywacji skryptu.<br>
    Wróć do formularza i spróbuj ponownie.<br>
    Jeżeli błąd będzie nieustępliwy, daj mi znać na {$settings['email']['blogContactMail']}");
define('EXITMSG_BADCAPTHAEMAIL', "Musisz dodać antybotowy kod do swojego adresu.
    Przeczytaj uważniej instrukcję tuż nad polem formularza.");
define('EXITMSG_EMAILNOTFOUND', "Nie znalazłem tego adresu emailowego na liście subskrybentów.");
define('EXITMSG_SUBSCRIBERLISTNOTFOUND', "Wskazana lista subskrybentów nie istnieje.");
define('EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT', "Skrypt nie został uruchomiony.<br>
    Czy link do odwołania subskrybcji jest poprawny?<br>Skąd żeś go wziął?");
define('EXITMSG_REMOVEDSUBSCRIBER', " został usunięty z listy subskrybentów ");
define('EXITMSG_CANNOTREMOVESUBSCRIBER', "Niezgodność haseł. Nie mogę usunąć ");
define('EXITMSG_WRONGCOMMENTADMINPASSWORD', "Nieprawidłowe hasło do edycji komentarzy jako admin.");
define('EXITMSG_WRONGCOMMENTID', "Błąd! Nie znaleziono komentarza do edycji.
    Data albo kod komentarza w linku jest niepoprawny.");
define('EXITMSG_TOOLATETOEDITCOMMENT', "Już po ptokach. Na edycję tego komentarza jest za późno.");
define('EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD', "Niepoprawne użycie skryptu... hakierze!");
define('EXITMSG_NOSEARCHPHRASE', "Brak frazy do wyszukania");
define('EXITMSG_INVALIDACTION', "Comecon nie potrafi tego zrobić");
define('EXITMSG_FILEUNREADABLE', "Nie mogę odczytać pliku");
define('EXITMSG_NOTSTRING', "Spodziewałem się stringa, a dostałem Bóg raczy wiedzieć co");
define('EXITMSG_WRONGREQUESTMETHOD', "GET/POST mi się tutaj nie zgadza");
define('EXITMSG_KEYISWRONG', "Brakuje klucza albo jego wartość nie jest stringiem");
define('EXITMSG_NOTIFICATIONERROR', "Nie mogę stworzyć powiadomienia mailowego z powodu złych parametrów");
define('EXITMSG_ADMINCOMMENTPASSWORD', "Hasło admina do edytowania komentarzy nie zostało wybrane, dodaj je w ustawieniach");
define('EXITMSG_COOKIEKEY', "Klucz do ciasteczek nie został wygenerowany, dodaj go w ustawieniach");
define('MSG_COMMENTINCONTEXT', "Komentarz mógł w międzyczasie zostać zmieniony lub usunięty.
    Wklikaj się we wpis na blogu, żeby odnaleźć jego najnowszą wersję.");
define('MSG_COMMENTFEEDENTRYTITLE', "Komentarz do wpisu:");
define('LABEL_EDITCOMMENTTITLE', "Edycja komentarza");
define('LABEL_EDITCOMMENTFIELD', "Edytuj swój komentarz.<br>Jeżeli chcesz go usunąć,
    skasuj wszystko i potwierdź edycję przyciskiem.");
define('LABEL_EDITCOMMENTBUTTON', "Potwierdź edycję");
define('LABEL_SEARCHTITLE', "Szukana fraza");
define('LABEL_SEARCHRESULT', "Ilość stron z szukaną frazą w treści");
define('LABEL_SEARCHRESULTCOMMENTS', "Ilość stron z szukaną frazą w komentarzach");
