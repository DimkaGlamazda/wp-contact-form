<?php

class ContactFormHandler
{
	private static $errors = [];
	private static $is_success = false;

	public static function get()
	{
		if(isset(self::$errors['general']))
		{
			?>
			<div class="alert alert-danger">
				<?=self::$errors['general']?>
			</div>
			<?php
		}

		if(self::$is_success)
		{
			?>
			<div class="alert alert-success">
				<p>Thank you for your message! Your action is very important for us!</p>
			</div>
			<?php
		}

		?>
		<form action="<?=esc_url( $_SERVER['REQUEST_URI'] )?>" method="post">
			<div class="fsection-form-row">
				<div class="form-column">
					<label for="input-1" class="label-required">お名前（ご担当者様名）</label>
					<input name="u-name" type="text" id="input-1" value="<?=self::value('u-name')?>">
					<span class="contact-error-caption"><?=isset(self::$errors['u-name']) ? self::$errors['u-name'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-2" class="label-required">会社名</label>
					<input name="u-company" type="text" id="input-2" value="<?=self::value('u-company')?>">
					<span class="contact-error-caption"><?=isset(self::$errors['u-company']) ? self::$errors['u-company'] : null?></span>
				</div>
				<div class="form-column">
					<label for="input-3">部署名</label>
					<input name="u-area" type="text" id="input-3" value="<?=self::value('u-area')?>">
					<span class="contact-error-caption"><?=isset(self::$errors['u-area']) ? self::$errors['u-area'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-4" class="label-required">メールアドレス</label>
					<input name="u-email" type="text" id="input-4" value="<?=self::value('u-email')?>">
					<span class="contact-error-caption"><?=isset(self::$errors['u-email']) ? self::$errors['u-email'] : null?></span>
				</div>
				<div class="form-column">
					<label for="input-5" class="label-required">お電話番号</label>
					<input name="u-phone" type="text" id="input-5" value="<?=self::value('u-phone')?>">
					<span class="contact-error-caption"><?=isset(self::$errors['u-phone']) ? self::$errors['u-phone'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<div class="form-column">
					<label for="input-6">こちらにお問い合わせ内容をご記入ください。</label>
					<textarea name="u-message" id="input-6"><?=self::value('u-message')?></textarea>
					<span class="contact-error-caption"><?=isset(self::$errors['u-message']) ? self::$errors['u-message'] : null?></span>
				</div>
			</div>
			<div class="section-form-row">
				<input name="u-submit" type="submit" value="送信">
			</div>
		</form>
		<?php
	}

	public static function send()
	{
		if(!is_null(self::value('u-submit')))
		{

			$name = sanitize_text_field(self::value('u-name'));

			if(! self::value('u-name'))
			{
				self::$errors['u-name'] = 'Required field';
			}

			$company = sanitize_text_field(self::value('u-company'));

			if(! self::value('u-company'))
			{
				self::$errors['u-company'] = 'Required field';
			}

			$area = sanitize_text_field(self::value('u-area'));

			$email = sanitize_email(self::value('u-email'));

			if(! self::value('u-email'))
			{
				self::$errors['u-email'] = 'Required field';
			}

			$phone = sanitize_text_field(self::value('u-phone'));


			if( ! self::value('u-phone'))
			{
				self::$errors['u-phone'] = 'Required field';
			}

			$message = esc_textarea(self::value('u-message'));

			if(! self::value('u-message'))
			{
				self::$errors['u-message'] = 'Required field';
			}

			if(empty(self::$errors))
			{
				$to = get_option( 'admin_email' );

				$headers = "From: $name <$email>" . "\r\n";
				$headers .= "Company: {$company}" . "\r\n";
				$headers .= "Area: {$area}" . "\r\n";
				$headers .= "Phome: {$phone}" . "\r\n";


				if ( wp_mail( $to, "Email from $name", $message, $headers ) )
				{
					self::$is_success = true;
				}
				else
				{
					self::$errors['general'] = 'Something went wrong. Please try again later.';
				}
			}
		}
	}

	private static function value($key)
	{
		return isset($_POST[$key]) ? trim(esc_attr($_POST[$key])) : null;
	}
}