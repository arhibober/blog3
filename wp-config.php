<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'blog3');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uN*>Af0+G:g,AF+M.K-RUh*Zr@_9S CU:MBz--)ZQhZ[g=za@[n/u<lIhw0GzxSJ');
define('SECURE_AUTH_KEY',  'iK{wD-fd^knnR^_XZSIOw=Y[/|;ovqj^O-u_Tg]Bm^!=o2}]pAXCP2)A4k*WcZB9');
define('LOGGED_IN_KEY',    'r?jh ^nhYqc1M<x%o1F!`TvPt|sIpHPHj`D.oIOM((Ja(hzO;NpD8k3<of%MJ$&]');
define('NONCE_KEY',        'GM@Z0j=.1Yo++[&c<$7duh^AexCny;*}y*+y~iJIr{-Va2Ku*]!S7zZC~@Zoj6<K');
define('AUTH_SALT',        'q$TD3k!vb@ESsJQ>QCZ8DVWR}jzHAv+!sf#AS/{2$zsMGVZrBgP1[7Y9U0t]f[.e');
define('SECURE_AUTH_SALT', 'T4B&dcuHG;-2<l/w;x5~6dDY_WQpOISK0Yp&*`J`Cmx,3#q%71;WZaB*8Y_:(y%r');
define('LOGGED_IN_SALT',   '+3=u~|Tiy:dSsxSyz:HgCm,^tdTS -2QyVEMGf6vG&>|L*P+zO29M9[IgY d)7@m');
define('NONCE_SALT',       'c.eIUk=3~pV4N]d2Ncp8]~?IQyvGMiiPrbcCu!N)/fnrY$o%mo@K>;UpLphQlYaH');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
