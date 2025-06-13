import mailbox
import os

MAILBOX = '/var/mail/bornik'
OUTPUT_DIR = 'emails_html'

os.makedirs(OUTPUT_DIR, exist_ok=True)

mbox = mailbox.mbox(MAILBOX)

for idx, msg in enumerate(mbox):
    subject = msg['subject']
    sender = msg['from']
    date = msg['date']
    html_found = False

    if msg.is_multipart():
        for part in msg.walk():
            if part.get_content_type() == 'text/html':
                html = part.get_payload(decode=True).decode(errors='ignore')
                html_found = True
                break
    else:
        if msg.get_content_type() == 'text/html':
            html = msg.get_payload(decode=True).decode(errors='ignore')
            html_found = True

    if html_found:
        filename = f"{OUTPUT_DIR}/email_{idx+1}.html"
        with open(filename, 'w', encoding='utf-8') as f:
            f.write(f"<h2>From: {sender}</h2>\n")
            f.write(f"<h3>Subject: {subject}</h3>\n")
            f.write(f"<h4>Date: {date}</h4>\n")
            f.write(html)
        print(f"Saved: {filename}")
    else:
        print(f"Email {idx+1}: No HTML part found.")
