# Mailtrap Configuration for Testing Email
# This is perfect for development - catches all emails in a testing inbox

# Sign up at https://mailtrap.io (free tier available)
# Get your SMTP credentials from the inbox settings

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gomoto.com
MAIL_FROM_NAME="GoMOTO"

# Benefits:
# - Free testing tier
# - All emails caught in web interface
# - No risk of sending real emails during development
# - Email preview and HTML inspection tools
