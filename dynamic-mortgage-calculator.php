<?php
/**
 * Plugin Name: Dinamik Mortgage Hesaplayıcı
 * Plugin URI:  https://github.com/hakanscotland/mortgage-calculator/
 * Description: Dinamik parametrelere sahip mortgage hesaplama eklentisi.
 * Version:     1.0.3
 * Author:      Hakan Dag
 * Author URI:  https://www.secondmedia.co.uk
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dynamic-mortgage-calculator
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Güvenlik için doğrudan erişimi engelle
}

// Eklenti dil dosyalarını yükle
function dmc_load_textdomain() {
	load_plugin_textdomain( 'dynamic-mortgage-calculator', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'dmc_load_textdomain' );

// Ayarlar Menüsü
add_action( 'admin_menu', 'dmc_add_admin_menu' );
function dmc_add_admin_menu() {
	add_menu_page(
		__( 'Mortgage Ayarları', 'dynamic-mortgage-calculator' ),
		__( 'Mortgage Ayarları', 'dynamic-mortgage-calculator' ),
		'manage_options',
		'dynamic-mortgage-settings',
		'dmc_settings_page',
		'dashicons-admin-home',
		60
	);
}

// Ayarlar Sayfası İçeriği
function dmc_settings_page() {
	?>
	<div class="wrap">
		<h1><?php _e( 'Dinamik Mortgage Hesaplayıcı Ayarları', 'dynamic-mortgage-calculator' ); ?></h1>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'dmc_settings_group' );
				do_settings_sections( 'dynamic-mortgage-settings' );
				submit_button( __( 'Ayarları Kaydet', 'dynamic-mortgage-calculator' ) );
			?>
		</form>
	</div>
	<?php
}

// Ayar Bölümleri ve Alanları
add_action( 'admin_init', 'dmc_register_settings' );
function dmc_register_settings() {
	register_setting( 'dmc_settings_group', 'dmc_dynamic_fields', 'dmc_sanitize_dynamic_fields' ); // Dinamik Alanlar Ayarı
	register_setting( 'dmc_settings_group', 'dmc_plugin_language', 'dmc_sanitize_language' ); // Dil Ayarı
	register_setting( 'dmc_settings_group', 'dmc_mortgage_types', 'dmc_sanitize_mortgage_types' ); // Mortgage Türleri Ayarı
	register_setting( 'dmc_settings_group', 'dmc_mortgage_purposes', 'dmc_sanitize_mortgage_purposes' ); // Mortgage Amaçları Ayarı
	register_setting( 'dmc_settings_group', 'dmc_uk_current_rates_info', 'dmc_sanitize_uk_current_rates_info' ); // UK Güncel Oranları Bilgi Ayarı

	// Genel Ayarlar Bölümü (İsteğe bağlı, düzenlenebilir)
	add_settings_section(
		'dmc_general_settings_section',
		__( 'Genel Ayarlar', 'dynamic-mortgage-calculator' ),
		'dmc_general_settings_section_info',
		'dynamic-mortgage-settings'
	);

	// Dil Ayarları Alanı (Genel Ayarlar Bölümüne taşıdık)
	add_settings_field(
		'dmc_plugin_language',
		__( 'Eklenti Dili', 'dynamic-mortgage-calculator' ),
		'dmc_language_select_field',
		'dynamic-mortgage-settings',
		'dmc_general_settings_section'
	);

	// Dinamik Alanlar Bölümü
	add_settings_section(
		'dmc_dynamic_fields_section',
		__( 'Dinamik Parametre Alanları', 'dynamic-mortgage-calculator' ),
		'dmc_dynamic_fields_section_info',
		'dynamic-mortgage-settings'
	);

	add_settings_field(
		'dmc_dynamic_fields',
		__( 'Dinamik Alanlar', 'dynamic-mortgage-calculator' ),
		'dmc_dynamic_fields_field',
		'dynamic-mortgage-settings',
		'dmc_dynamic_fields_section'
	);

	// Mortgage Türleri Bölümü
	add_settings_section(
		'dmc_mortgage_types_section',
		__( 'Mortgage Türleri', 'dynamic-mortgage-calculator' ),
		'dmc_mortgage_types_section_info',
		'dynamic-mortgage-settings'
	);

	add_settings_field(
		'dmc_mortgage_types',
		__( 'Mevcut Mortgage Türleri', 'dynamic-mortgage-calculator' ),
		'dmc_mortgage_types_field',
		'dynamic-mortgage-settings',
		'dmc_mortgage_types_section'
	);

	// Mortgage Amaçları Bölümü
	add_settings_section(
		'dmc_mortgage_purposes_section',
		__( 'Mortgage Amaçları', 'dynamic-mortgage-calculator' ),
		'dmc_mortgage_purposes_section_info',
		'dynamic-mortgage-settings'
	);

	add_settings_field(
		'dmc_mortgage_purposes',
		__( 'Mevcut Mortgage Amaçları', 'dynamic-mortgage-calculator' ),
		'dmc_mortgage_purposes_field',
		'dynamic-mortgage-settings',
		'dmc_mortgage_purposes_section'
	);

	// UK Güncel Oranları Bilgi Bölümü
	add_settings_section(
		'dmc_uk_current_rates_section',
		__( 'UK Güncel Oranları Bilgisi', 'dynamic-mortgage-calculator' ),
		'dmc_uk_current_rates_section_info',
		'dynamic-mortgage-settings'
	);

	add_settings_field(
		'dmc_uk_current_rates_info',
		__( 'Güncel Oranlar Açıklaması', 'dynamic-mortgage-calculator' ),
		'dmc_uk_current_rates_info_field',
		'dynamic-mortgage-settings',
		'dmc_uk_current_rates_section'
	);
}

// Genel Ayarlar Bölümü Açıklaması (İsteğe bağlı)
function dmc_general_settings_section_info() {
	_e( 'Eklentinin genel ayarlarını buradan yapılandırabilirsiniz.', 'dynamic-mortgage-calculator' );
}

// Dinamik Alanlar Bölümü Açıklaması
function dmc_dynamic_fields_section_info() {
	_e( 'Mortgage hesaplama formunda gösterilecek dinamik parametre alanlarını burada yönetebilirsiniz. Alan ekleyebilir, düzenleyebilir veya silebilirsiniz.', 'dynamic-mortgage-calculator' );
}

// Dinamik Alanlar Alanı Görüntüleme Fonksiyonu (Basit Metin Alanı - Geliştirilecek)
function dmc_dynamic_fields_field() {
	$fields = get_option( 'dmc_dynamic_fields', '' );
	?>
	<textarea name="dmc_dynamic_fields" id="dmc_dynamic_fields" rows="3" cols="50"><?php echo esc_textarea( $fields ); ?></textarea>
	<p class="description"><?php _e( 'Her satıra bir dinamik alan adı girin. Formda ek girdi alanları olarak görüneceklerdir.', 'dynamic-mortgage-calculator' ); ?></p>
	<p class="description"><?php _e( 'Örnek: "Bölge", "Promosyon Kodu", "Referans No"', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// Dinamik Alanları Temizleme ve Doğrulama (Geliştirilecek)
function dmc_sanitize_dynamic_fields( $input ) {
	return sanitize_textarea_field( $input );
}

// Dil Seçim Alanı Görüntüleme Fonksiyonu
function dmc_language_select_field() {
	$selected_language = get_option( 'dmc_plugin_language', 'en_GB' ); // Varsayılan İngilizce
	?>
	<select name="dmc_plugin_language" id="dmc_plugin_language">
		<option value="en_GB" <?php selected( $selected_language, 'en_GB' ); ?>>English</option>
		<option value="tr_TR" <?php selected( $selected_language, 'tr_TR' ); ?>>Türkçe</option>
	</select>
	<p class="description"><?php _e( 'Eklenti için kullanılacak dili seçin.', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// Dil Seçimini Temizleme ve Doğrulama
function dmc_sanitize_language( $input ) {
	$allowed_languages = array( 'en_GB', 'tr_TR' );
	if ( in_array( $input, $allowed_languages ) ) {
		return $input;
	}
	return 'en_GB'; // Geçersiz giriş durumunda varsayılan dil
}

// Mortgage Türleri Bölümü Açıklaması
function dmc_mortgage_types_section_info() {
	_e( 'Hesaplama formunda ve açıklamalarda kullanılacak mortgage türlerini yönetin. Bunlar bilgilendirme amaçlıdır.', 'dynamic-mortgage-calculator' );
	echo '<br>';
	_e( 'Örnek Türler: Sabit Faizli, Değişken Faizli, Sadece Faiz Ödemeli vb.', 'dynamic-mortgage-calculator' );
}

// Mortgage Türleri Alanı Görüntüleme Fonksiyonu
function dmc_mortgage_types_field() {
	$types_str = get_option( 'dmc_mortgage_types', "Sabit Faizli\nDeğişken Faizli" ); // Varsayılan türler
	?>
	<textarea name="dmc_mortgage_types" id="dmc_mortgage_types" rows="3" cols="50"><?php echo esc_textarea( $types_str ); ?></textarea>
	<p class="description"><?php _e( 'Her satıra bir mortgage türü girin.', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// Mortgage Türlerini Temizleme ve Doğrulama
function dmc_sanitize_mortgage_types( $input ) {
	return sanitize_textarea_field( $input );
}

// Mortgage Amaçları Bölümü Açıklaması
function dmc_mortgage_purposes_section_info() {
	_e( 'Mortgage hesaplama formunda ve açıklamalarda kullanılacak mortgage amaçlarını yönetin. Bunlar bilgilendirme amaçlıdır.', 'dynamic-mortgage-calculator' );
	echo '<br>';
	_e( 'Örnek Amaçlar: İlk Ev Sahibi, Ev Değiştirme, Yeniden Mortgage, Yatırım Amaçlı Kiralama vb.', 'dynamic-mortgage-calculator' );
}

// Mortgage Amaçları Alanı Görüntüleme Fonksiyonu
function dmc_mortgage_purposes_field() {
	$purposes_str = get_option( 'dmc_mortgage_purposes', "İlk Ev Sahibi\nEv Değiştirme\nYeniden Mortgage\nYatırım Amaçlı Kiralama" ); // Varsayılan amaçlar
	?>
	<textarea name="dmc_mortgage_purposes" id="dmc_mortgage_purposes" rows="3" cols="50"><?php echo esc_textarea( $purposes_str ); ?></textarea>
	<p class="description"><?php _e( 'Her satıra bir mortgage amacı girin.', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// Mortgage Amaçlarını Temizleme ve Doğrulama
function dmc_sanitize_mortgage_purposes( $input ) {
	return sanitize_textarea_field( $input );
}

// UK Güncel Oranları Bilgi Bölümü Açıklaması
function dmc_uk_current_rates_section_info() {
	_e( 'UK güncel mortgage oranları hakkında bilgi veya açıklama ekleyin. Dinamik veri entegrasyonu bu sürümde bulunmamaktadır, statik bilgi girebilirsiniz.', 'dynamic-mortgage-calculator' );
	echo '<br>';
	_e( 'Örneğin, güncel ortalama oranlar, kaynaklar veya genel piyasa durumu hakkında bilgi verebilirsiniz.', 'dynamic-mortgage-calculator' );
}

// UK Güncel Oranları Bilgi Alanı Görüntüleme Fonksiyonu
function dmc_uk_current_rates_info_field() {
	$info = get_option( 'dmc_uk_current_rates_info', '' );
	?>
	<textarea name="dmc_uk_current_rates_info" id="dmc_uk_current_rates_info" rows="4" cols="70"><?php echo esc_textarea( $info ); ?></textarea>
	<p class="description"><?php _e( 'UK güncel mortgage oranları hakkında açıklayıcı metin (statik içerik).', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// UK Güncel Oranları Bilgilerini Temizleme ve Doğrulama
function dmc_sanitize_uk_current_rates_info( $input ) {
	return wp_kses_post( $input ); // wp_kses_post daha güvenli ve zengin metin içeriği için
}


// Kısa Kod (Shortcode) Fonksiyonu
add_shortcode( 'dynamic_mortgage_calculator', 'dmc_calculator_shortcode' );
function dmc_calculator_shortcode( $atts ) {
	ob_start();

	$mortgage_types_str = get_option( 'dmc_mortgage_types', "Sabit Faizli\nDeğişken Faizli" );
	$mortgage_types = explode("\n", $mortgage_types_str);
	$mortgage_purposes_str = get_option( 'dmc_mortgage_purposes', "İlk Ev Sahibi\nEv Değiştirme\nYeniden Mortgage\nYatırım Amaçlı Kiralama" );
	$mortgage_purposes = explode("\n", $mortgage_purposes_str);
	$uk_rates_info = get_option( 'dmc_uk_current_rates_info', '' );

	?>
	<div class="dynamic-mortgage-calculator-form">
		<h2><?php _e( 'Mortgage Hesaplama', 'dynamic-mortgage-calculator' ); ?></h2>

		<?php if (!empty($uk_rates_info)): ?>
			<div class="uk-current-rates-info">
				<h3><?php _e('UK Güncel Oranları Hakkında Bilgi', 'dynamic-mortgage-calculator'); ?></h3>
				<?php echo wp_kses_post($uk_rates_info); // Güvenli HTML içeriği gösterimi ?>
			</div>
		<?php endif; ?>

		<form id="mortgage-form">
			<label for="loan_amount"><?php _e( 'Kredi Tutarı:', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" id="loan_amount" name="loan_amount" required><br><br>

			<label for="interest_rate"><?php _e( 'Yıllık Faiz Oranı (%):', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" step="0.01" id="interest_rate" name="interest_rate" required><br><br>

			<label for="loan_term"><?php _e( 'Vade Süresi (Yıl):', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" id="loan_term" name="loan_term" required><br><br>

			<?php
				$dynamic_fields_str = get_option( 'dmc_dynamic_fields', '' );
				$dynamic_fields = explode("\n", $dynamic_fields_str);

				foreach ($dynamic_fields as $field_name) {
					$field_name = trim($field_name);
					if (!empty($field_name)):
			?>
						<label for="<?php echo sanitize_title($field_name); ?>"><?php echo esc_html($field_name); ?>:</label>
						<input type="text" id="<?php echo sanitize_title($field_name); ?>" name="<?php echo sanitize_title($field_name); ?>"><br><br>
			<?php
					endif;
				}
			?>

			<button type="button" id="calculate_mortgage"><?php _e( 'Hesapla', 'dynamic-mortgage-calculator' ); ?></button>
		</form>

		<div id="calculation-results" style="margin-top: 20px;">
			<!-- Hesaplama sonuçları buraya gelecek -->
		</div>

		<div class="mortgage-types-purposes-info" style="margin-top: 20px;">
			<?php if (!empty($mortgage_types)): ?>
				<h4><?php _e('Mortgage Türleri:', 'dynamic-mortgage-calculator'); ?></h4>
				<ul>
					<?php foreach ($mortgage_types as $type): ?>
						<li><?php echo esc_html(trim($type)); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if (!empty($mortgage_purposes)): ?>
				<h4><?php _e('Mortgage Amaçları:', 'dynamic-mortgage-calculator'); ?></h4>
				<ul>
					<?php foreach ($mortgage_purposes as $purpose): ?>
						<li><?php echo esc_html(trim($purpose)); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	</div>

	<script type="text/javascript">
		document.getElementById('calculate_mortgage').addEventListener('click', function() {
			var loanAmount = parseFloat(document.getElementById('loan_amount').value);
			var interestRate = parseFloat(document.getElementById('interest_rate').value);
			var loanTerm = parseInt(document.getElementById('loan_term').value);

			if (isNaN(loanAmount) || isNaN(interestRate) || isNaN(loanTerm)) {
				alert('Lütfen geçerli sayısal değerler girin.');
				return;
			}

			var monthlyInterestRate = interestRate / 12 / 100;
			var numberOfPayments = loanTerm * 12;

			var monthlyPayment = (loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) / (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
			var totalPayment = monthlyPayment * numberOfPayments;
			var totalInterest = totalPayment - loanAmount;

			var resultsHTML = '<h3><?php _e( 'Hesaplama Sonuçları', 'dynamic-mortgage-calculator' ); ?></h3>';
			resultsHTML += '<p><strong><?php _e( 'Aylık Taksit:', 'dynamic-mortgage-calculator' ); ?></strong> ' + monthlyPayment.toFixed(2) + '</p>';
			resultsHTML += '<p><strong><?php _e( 'Toplam Geri Ödeme:', 'dynamic-mortgage-calculator' ); ?></strong> ' + totalPayment.toFixed(2) + '</p>';
			resultsHTML += '<p><strong><?php _e( 'Toplam Faiz:', 'dynamic-mortgage-calculator' ); ?></strong> ' + totalInterest.toFixed(2) + '</p>';

			document.getElementById('calculation-results').innerHTML = resultsHTML;
		});
	</script>
	<?php

	return ob_get_clean();
}