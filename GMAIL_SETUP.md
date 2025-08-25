# Gmail SMTP Configuration for GoMOTO
# Replace your current .env mail settings with these:

# For Gmail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="GoMOTO"

# Important Notes:
# 1. You need to enable 2-factor authentication on your Gmail account
# 2. Generate an "App Password" from Google Account settings
# 3. Use the App Password, NOT your regular Gmail password
# 4. Steps to get App Password:
#    - Go to Google Account settings
#    - Security > 2-Step Verification
#    - App passwords > Generate app password for "Mail"
#    - Use that 16-character password in MAIL_PASSWORD
