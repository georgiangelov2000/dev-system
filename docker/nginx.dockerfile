FROM nginx:latest

# Create the /etc/pwd directory
RUN mkdir -p /etc/pwd

# Install apache2-utils (containing htpasswd command) and procps
RUN apt-get update && apt-get install --no-install-recommends -y apache2-utils procps

# Generate .htpasswd with the username and password
RUN htpasswd -b -c /etc/pwd/.htpasswd georgi angelov2000
