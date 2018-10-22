<?php

class ContactFormHandler
{

	function __construct() {}

	private $errors = [];
	private $is_success = false;

	public function get()
	{

		if(isset($this->errors['general']))
		{
			?>
			<div class="alert alert-danger">
				<?=$this->errors['general']?>
			</div>
			<?php
		}

		if($this->is_success)
		{
			?>
			<div class="alert alert-success">
				<p>Thank you for your message! Your action is very important for us!</p>
			</div>
			<?php
		}

		?>
		<form id="form-id" action="<?=esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">

			<div class="fsection-form-row">
				<div class="form-column">
					<label for="input-1" class="label-required">お名前（ご担当者様名）</label>
					<input name="u-name" type="text" id="input-1" value="<?=$this->is_success ? null : $this->value('u-name')?>">
					<span class="contact-error-caption"><?=isset($this->errors['u-name']) ? $this->errors['u-name'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-2" class="label-required">会社名</label>
					<input name="u-company" type="text" id="input-2" value="<?=$this->is_success ? null : $this->value('u-company')?>">
					<span class="contact-error-caption"><?=isset($this->errors['u-company']) ? $this->errors['u-company'] : null?></span>
				</div>
				<div class="form-column">
					<label for="input-3">部署名</label>
					<input name="u-area" type="text" id="input-3" value="<?=$this->is_success ? null : $this->value('u-area')?>">
					<span class="contact-error-caption"><?=isset($this->errors['u-area']) ? $this->errors['u-area'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-4" class="label-required">メールアドレス</label>
					<input name="u-email" type="text" id="input-4" value="<?=$this->is_success ? null : $this->value('u-email')?>">
					<span class="contact-error-caption"><?=isset($this->errors['u-email']) ? $this->errors['u-email'] : null?></span>
				</div>
				<div class="form-column">
					<label for="input-5" class="label-required">お電話番号</label>
					<input name="u-phone" type="text" id="input-5" value="<?=$this->is_success ? null : $this->value('u-phone')?>">
					<span class="contact-error-caption"><?=isset($this->errors['u-phone']) ? $this->errors['u-phone'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-6">こちらにお問い合わせ内容をご記入ください。</label>
					<textarea name="u-message" id="input-6"><?=$this->is_success ? null : $this->value('u-message')?></textarea>
					<span class="contact-error-caption"><?=isset($this->errors['u-message']) ? $this->errors['u-message'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<input class="g-recaptcha" data-sitekey="6LczMXYUAAAAAMzydX5JjzlzYaOC1Pzr-F53N5W8" type="submit" data-callback='onSubmit' value="送信">
			</div>
			<input type="hidden" name="u-submit" value="1">
			<?php wp_nonce_field( 'contact_form' ); ?>
		</form>
		<script>

			function onSubmit(token) {
                document.getElementById("form-id").submit();
            }

		</script>
		<?php
	}

	public function send()
	{
		if(! is_null($this->value('u-submit')))
		{

			$nonce = $_REQUEST['_wpnonce'];

			if ( ! wp_verify_nonce( $nonce, 'contact_form' ) ) {
				die("Unable access");
			}

			$name = sanitize_text_field($this->value('u-name'));

			if(! $this->value('u-name'))
			{
				$this->errors['u-name'] = 'Required field';
			}

			$company = sanitize_text_field($this->value('u-company'));

			if(! $this->value('u-company'))
			{
				$this->errors['u-company'] = 'Required field';
			}

			$area = sanitize_text_field($this->value('u-area'));

			$email = sanitize_email($this->value('u-email'));


			if(! filter_var($this->value('u-email'), FILTER_VALIDATE_EMAIL))
			{
				$this->errors['u-email'] = 'Invalid email address';
			}

			if(! $this->value('u-email'))
			{
				$this->errors['u-email'] = 'Required field';
			}

			$phone = sanitize_text_field($this->value('u-phone'));

			if( ! $this->value('u-phone'))
			{
				$this->errors['u-phone'] = 'Required field';
			}

			$message = esc_textarea($this->value('u-message'));

			if(! $this->value('u-message'))
			{
				$this->errors['u-message'] = 'Required field';
			}

			if(empty($this->errors))
			{
				$to = get_option( 'admin_email' );

				$headers = "From: $name <$email>" . "\r\n";

				$letter  = "Name: {$name}\n";
				$letter .= "Company: {$company}\n";
				$letter .= "Phone: {$phone}" . "\n";
				$letter .= "Area: {$area}" . "\n";

				$letter .= "\n\n\n";

				$letter .= $message;

				if ( wp_mail( $to, "Email from $name", $letter, $headers ))
				{
					$this->is_success = true;
				}
				else
				{
					$this->errors['general'] = 'Something went wrong. Please try again later.';
				}
			}
		}
	}

	private function value($key)
	{
		return isset($_POST[$key]) ? trim(esc_attr($_POST[$key])) : null;
	}
}