<section class="settings">
    <nav class="settings__nav">
        <span class="settings__text">Основное</span>
        <ul class="settings__list">
            <li class="settings__list-item">
                <img src="/media/image/mainpage.svg" alt="ИконкаГлавная">
                <a href="/general_page.php" class="settings__link">Главная</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/ticket.svg" alt="ИконкаИсполнителей">
                <a href="/users/artist_page.php" class="settings__link">Исполнители</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/computer.svg" alt="ИконкаЖанров">
                <a href="/users/genre_page.php" class="settings__link">Жанры</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/playback.svg" alt="ИконкаКомпозиции">
                <a href="/users/search_music.php?criteria=composition" class="settings__link">Все композиции</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/analize.svg" alt="ИконкаАнализа">
                <a href="/users/analize_page.php" class="settings__link">Анализ композиции</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаПоиска">
                <a href="/users/search_music.php?criteria=name" class="settings__link">Поиск копозиции</a>
            </li>
        </ul>
        <span class="settings__text">Настройки</span>
        <ul class="settings__list">
            <?php
            if (!isset ($_SESSION['login'])) {
                echo '
            <li class="settings__list-item">
                <img src="/media/image/login.svg" alt="ИконкаПрофиля">
                <a href="/users/old_client.php" class="settings__link">Войти в профиль</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/reg.svg" alt="ИконкаРегистрации">
                <a href="registration.html" class="settings__link">Зарегистрироваться</a>
            </li>
            ';
            } else {
                echo '
            <li class="settings__list-item">
                <img src="/media/image/login.svg" alt="ИконкаПрофиля">
                <a href="/users/authorized/list_history.php" class="settings__link">История пользователя</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/settings.svg" alt="ИконкаИзмененияПрофиля">
                <a href="/users/authorized/update_profile.php" class="settings__link">Настройки профиля</a>
            </li>

            <li class="settings__list-item">
                <img src="/media/image/exit.svg" alt="ИконкаВыхода">
                <a href="/users/authorized/logout.php" class="settings__link">Выйти из профиля</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаПочты">
                <a href="/users/authorized/mail.php" class="settings__link">Написать в поддержку </a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/search.svg" alt="ИконкаГалочки">
                <a href="/users/authorized/list_mailing.php" class="settings__link">Управление подписками </a>
            </li>';
            }
            ?>
        </ul>
        <?php
        if (isset($_SESSION['right']) && $_SESSION['right'] == 1) {
            echo '
            <span class="settings__text">Администрирование</span>
            <ul class="settings__list">
            <li class="settings__list-item">
                <img src="/media/image/list.svg" alt="ИконкаСписка">
                <a href="/users/admin/list_comp.php" class="settings__link">Список композиций</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/list.svg" alt="ИконкаСписка">
                <a href="/users/admin/list_genre.php" class="settings__link">Список жанров</a>
            </li>
            <li class="settings__list-item">
            <img src="/media/image/list.svg" alt="ИконкаСписка">
            <a href="/users/admin/list_artist.php" class="settings__link">Список исполнителей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/users.svg" alt="ИконкаПользователей">
                <a href="/users/admin/list_user.php" class="settings__link">Список пользователей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаСтатистики">
                <a href="/users/admin/list_comp.php?criteria=1" class="settings__link">Статистика популярности</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаПочты">
                <a href="/users/admin/list_mail.php" class="settings__link">Сообщения от пользователей</a>
            </li>
            <li class="settings__list-item">
                <img src="/media/image/statistic.svg" alt="ИконкаПочты">
                <a href="/users/admin/list_subscriptions.php" class="settings__link">Список рассылок</a>
            </li>
            </ul>';
        }
        ?>
    </nav>
</section>