<?php
/*
Plugin Name: plugin meteo
Plugin URI: https://mon-siteweb.com/
Description: Infos météo en temps réel à Nantes
Author: JC
Version: 1.0
Author URI: http://mon-siteweb.com/
*/

// pour faire du css
function add_style()
{
    wp_register_style('meteo_css', plugins_url('style.css', __FILE__));
    wp_enqueue_style('meteo_css');
}
add_action('admin_init', 'add_style');

// pour pouvoir choisir la ville
function getCity()
{
    $city = get_option('city');
    $unit = get_option('unit');

    $Api = "https://api.openweathermap.org/data/2.5/weather?q=" . $city . "&units=" . $unit . "&lang=fr&appid=92c3fd34ea87fe572aaad5a6f99029fb";
    $getFile = file_get_contents($Api);
    $datas = json_decode($getFile);
    // var_dump($datas);
    printf("<div class='displayMeteo'><span id='nom'>" . $city . "</span>_" . $datas->main->temp . "  degrés_ " . $datas->weather[0]->description . "   <img src='http://openweathermap.org/img/wn/" . $datas->weather[0]->icon . ".png'></div>");
}
add_action('admin_notices', 'getCity');

// créer et enregistrer dans WP des options
add_action('admin_menu', 'pluginJC_create_menu');
function pluginJC_create_menu()
{
    //create new top-level menu
    add_menu_page('pluginJC Settings', 'Meteo', 'administrator', __FILE__, 'pluginJC_settings_page');

    //call register settings function
    add_action('admin_init', 'register_pluginJC_settings');
}
function register_pluginJC_settings()
{
    //register our settings
    register_setting('pluginJC-settings-group', 'city');
    register_setting('pluginJC-settings-group', 'unit');
}
function pluginJC_settings_page()
{
?>
    <div class="wrap meteoPage">
        <h1>pluginJC</h1>

        <form method="post" action="options.php">
            <?php settings_fields('pluginJC-settings-group'); ?>
            <?php do_settings_sections('pluginJC-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" id="thPlugin">city</th>
                    <td><input type="text" name="city" value="<?php echo esc_attr(get_option('city')); ?>" /></td>
                </tr>
            </table>
            <label for="unit-select">Selectionner unité température:</label>

            <select name="unit" id="units-select">
                <option value="">--choix unité--</option>
                <!-- pour garder à l'ecran l'unité choisie après submit: if et echo selected -->
                <option <?php if (get_option('unit') == "metric") {
                            echo "selected";
                        } ?> value="metric">Celsius</option>
                <!-- même chose ici mais avec ternaire, pas besoin de echo grâce à la balise ?= et non php -->
                <option <?= get_option('unit') == "imperial" ? "selected" : "" ?> value="imperial">Farenheit</option>
                <option <?= get_option('unit') == "kelvin" ? "selected" : "" ?> value="kelvin">Kelvin</option>
            </select>
            <?php submit_button(); ?>
        </form>
    </div>
<?php } ?>