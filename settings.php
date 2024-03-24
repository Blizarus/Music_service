<section class="settings">
    <nav class="settings__nav">
        <span class="settings__text">Основное</span>
        <ul class="settings__list">
            <li class="settings__list-item">
                <img src="/media/image/mainpage.svg" alt="ИконкаГлавная" style="width: 25px; height: 25px;">
                <a href="/general_page.php" class="settings__link">Главная</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/ticket.svg" alt="ИконкаИсполнителей" style="width: 25px; height: 25px;">
                <a href="/users/artist_page.php" class="settings__link">Исполнители</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/computer.svg" alt="ИконкаЖанров" style="width: 25px; height: 25px;">
                <a href="/users/genre_page.php" class="settings__link">Жанры</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/playback.svg" alt="ИконкаКомпозиции" style="width: 25px; height: 25px;">
                <a href="/users/search_music.php?criteria=composition" class="settings__link">Все композиции</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/analize.svg" alt="ИконкаАнализа" style="width: 25px; height: 25px;">
                <a href="/users/analize_page.php" class="settings__link">Анализ композиции</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаПоиска" style="width: 25px; height: 25px;">
                <a href="/users/search_music.php?criteria=name" class="settings__link">Поиск копозиции</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/forum.svg" alt="ИконкаФорума" style="width: 25px; height: 25px;">
                <a href="/users/forum_page.php?criteria=name" class="settings__link">Форум</a>
            </li>
        </ul>
        <span class="settings__text">Настройки</span>
        <ul class="settings__list">
            <?php
            if (!isset ($_SESSION['login'])) {
                echo '
            <li class="settings__list-item">
                <img src="/media/image/login.svg" alt="ИконкаПрофиля" style="width: 25px; height: 25px;">
                <a href="/users/old_client.php" class="settings__link">Войти в профиль</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/reg.svg" alt="ИконкаРегистрации" style="width: 25px; height: 25px;">
                <a href="registration.html" class="settings__link">Зарегистрироваться</a>
            </li>
            ';
            } else {
                echo '
            <li class="settings__list-item">
                <img src="/media/image/login.svg" alt="ИконкаПрофиля" style="width: 25px; height: 25px;">
                <a href="/users/authorized/list_history.php" class="settings__link">История пользователя</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/settings.svg" alt="ИконкаИзмененияПрофиля" style="width: 25px; height: 25px;">
                <a href="/users/authorized/update_profile.php" class="settings__link">Настройки профиля</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/exit.svg" alt="ИконкаВыхода" style="width: 25px; height: 25px;">
                <a href="/users/authorized/logout.php" class="settings__link">Выйти из профиля</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаПочты" style="width: 25px; height: 25px;">
                <a href="/users/authorized/mail.php" class="settings__link">Написать в поддержку </a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаГалочки" style="width: 25px; height: 25px;">
                <a href="/users/authorized/list_mailing.php" class="settings__link">Управление подписками </a>
            </li>';
            }
            ?>
        </ul>
        <?php
        if (isset ($_SESSION['right']) && $_SESSION['right'] == 1) {
            echo '
            <span class="settings__text">Администрирование</span>
            <ul class="settings__list">
            <li class="settings__list-item">
                <img src="/media/image/list.svg" alt="ИконкаСписка" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_comp.php" class="settings__link">Список композиций</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/list.svg" alt="ИконкаСписка" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_genre.php" class="settings__link">Список жанров</a>
            </li>
            <li class="settings__list-item">
            <img src="/media/image/list.svg" alt="ИконкаСписка" style="width: 25px; height: 25px;">
            <a href="/users/admin/list_artist.php" class="settings__link">Список исполнителей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/users.svg" alt="ИконкаПользователей" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_user.php" class="settings__link">Список пользователей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаСтатистики" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_comp.php?criteria=1" class="settings__link">Статистика популярности</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаПочты" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_mail.php" class="settings__link">Сообщения от пользователей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаПочты" style="width: 25px; height: 25px;">
                <a href="/users/admin/list_subscriptions.php" class="settings__link">Список рассылок</a>
            </li>
            </ul>';
        }
        ?>
    </nav>
</section>