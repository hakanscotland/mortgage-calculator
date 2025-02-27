<?php
/**
 * Plugin Name: Dinamik Mortgage Hesaplayıcı
 * Plugin URI:  https://example.com/
 * Description: Dinamik parametrelere sahip mortgage hesaplama eklentisi.
 * Version:     1.0.0
 * Author:      Adınız
 * Author URI:  https://example.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dynamic-mortgage-calculator
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Güvenlik için doğrudan erişimi engelle
}

// Eklenti kurulumu ve ayarları için fonksiyonlar buraya gelecek

// Ayarlar Menüsü
add_action( 'admin_menu', 'dmc_add_admin_menu' );
function dmc_add_admin_menu() {
	add_menu_page(
		__( 'Mortgage Ayarları', 'dynamic-mortgage-calculator' ), // Sayfa Başlığı
		__( 'Mortgage Ayarları', 'dynamic-mortgage-calculator' ), // Menü Başlığı
		'manage_options', // Yetki Seviyesi
		'dynamic-mortgage-settings', // Menü Slug
		'dmc_settings_page', // Fonksiyon
		'dashicons-admin-home', // İkon (isteğe bağlı)
		60 // Menü Pozisyonu (isteğe bağlı)
	);
}

// Ayarlar Sayfası İçeriği
function dmc_settings_page() {
	?>
	<div class="wrap">
		<h1><?php _e( 'Dinamik Mortgage Hesaplayıcı Ayarları', 'dynamic-mortgage-calculator' ); ?></h1>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'dmc_settings_group' ); // Ayar grubu adı
				do_settings_sections( 'dynamic-mortgage-settings' ); // Ayar bölümleri
				submit_button( __( 'Ayarları Kaydet', 'dynamic-mortgage-calculator' ) );
			?>
		</form>
	</div>
	<?php
}

// Ayar Bölümleri ve Alanları
add_action( 'admin_init', 'dmc_register_settings' );
function dmc_register_settings() {
	register_setting( 'dmc_settings_group', 'dmc_dynamic_fields', 'dmc_sanitize_dynamic_fields' ); // Dinamik Alanlar

	add_settings_section(
		'dmc_dynamic_fields_section', // Bölüm ID
		__( 'Dinamik Parametre Alanları', 'dynamic-mortgage-calculator' ), // Bölüm Başlığı
		'dmc_dynamic_fields_section_info', // Açıklama Fonksiyonu
		'dynamic-mortgage-settings' // Sayfa Slug
	);

	add_settings_field(
		'dmc_dynamic_fields', // Alan ID
		__( 'Dinamik Alanlar', 'dynamic-mortgage-calculator' ), // Alan Başlığı
		'dmc_dynamic_fields_field', // Alan Görüntüleme Fonksiyonu
		'dynamic-mortgage-settings', // Sayfa Slug
		'dmc_dynamic_fields_section' // Bölüm ID
	);
}

// Dinamik Alanlar Bölümü Açıklaması
function dmc_dynamic_fields_section_info() {
	_e( 'Mortgage hesaplama formunda gösterilecek dinamik parametre alanlarını burada yönetebilirsiniz. Alan ekleyebilir, düzenleyebilir veya silebilirsiniz.', 'dynamic-mortgage-calculator' );
}

// Dinamik Alanlar Alanı Görüntüleme Fonksiyonu (Basit Metin Alanı - Geliştirilecek)
function dmc_dynamic_fields_field() {
	$fields = get_option( 'dmc_dynamic_fields', '' ); // Kayıtlı alanları al
	?>
	<textarea name="dmc_dynamic_fields" id="dmc_dynamic_fields" rows="5" cols="50"><?php echo esc_textarea( $fields ); ?></textarea>
	<p class="description"><?php _e( 'Her satıra bir alan adı girin (virgülle ayırarak veya JSON formatında daha yapılandırılmış alanlar tanımlayabilirsiniz).', 'dynamic-mortgage-calculator' ); ?></p>
	<?php
}

// Dinamik Alanları Temizleme ve Doğrulama (Geliştirilecek)
function dmc_sanitize_dynamic_fields( $input ) {
	// Basitçe metni temizle şimdilik, daha gelişmiş doğrulama eklenecek
	return sanitize_textarea_field( $input );
}


// Kısa Kod (Shortcode) Fonksiyonu
add_shortcode( 'dynamic_mortgage_calculator', 'dmc_calculator_shortcode' );
function dmc_calculator_shortcode( $atts ) {
	// Form ve hesaplama mantığı buraya gelecek
	ob_start(); // Çıktı tamponlamayı başlat

	?>
	<div class="dynamic-mortgage-calculator-form">
		<h2>Mortgage Hesaplama</h2>
		<form id="mortgage-form">
			<label for="loan_amount"><?php _e( 'Kredi Tutarı:', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" id="loan_amount" name="loan_amount" required><br><br>

			<label for="interest_rate"><?php _e( 'Yıllık Faiz Oranı (%):', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" step="0.01" id="interest_rate" name="interest_rate" required><br><br>

			<label for="loan_term"><?php _e( 'Vade Süresi (Yıl):', 'dynamic-mortgage-calculator' ); ?></label>
			<input type="number" id="loan_term" name="loan_term" required><br><br>

			<?php
				// Dinamik Alanları Getir ve Formda Göster (Geliştirilecek)
				$dynamic_fields_str = get_option( 'dmc_dynamic_fields', '' );
				$dynamic_fields = explode("\n", $dynamic_fields_str); // Satır satır alanları ayır (basit örnek)

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
	</div>

	<script type="text/javascript">
		document.getElementById('calculate_mortgage').addEventListener('click', function() {
			var loanAmount = parseFloat(document.getElementById('loan_amount').value);
			var interestRate = parseFloat(document.getElementById('interest_rate').value);
			var loanTerm = parseInt(document.getElementById('loan_term').value);

			if (isNaN(loanAmount) || isNaN(interestRate) || isNaN(loanTerm)) {
				alert('Lütfen geçerli sayısal değerler girin.'); // Kullanıcıya hata mesajı
				return;
			}

			var monthlyInterestRate = interestRate / 12 / 100;
			var numberOfPayments = loanTerm * 12;

			var monthlyPayment = (loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) / (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
			var totalPayment = monthlyPayment * numberOfPayments;
			var totalInterest = totalPayment - loanAmount;

			var resultsHTML = '<h3>Hesaplama Sonuçları</h3>';
			resultsHTML += '<p><strong>Aylık Taksit:</strong> ' + monthlyPayment.toFixed(2) + '</p>';
			resultsHTML += '<p><strong>Toplam Geri Ödeme:</strong> ' + totalPayment.toFixed(2) + '</p>';
			resultsHTML += '<p><strong>Toplam Faiz:</strong> ' + totalInterest.toFixed(2) + '</p>';

			document.getElementById('calculation-results').innerHTML = resultsHTML;
		});
	</script>
	<?php

	return ob_get_clean(); // Tamponlanmış çıktıyı döndür
}