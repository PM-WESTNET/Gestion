FROM node
MAINTAINER Prakash Pandey <prakash.pandey@fulfil.io>

RUN cd /tmp \
	&& apt-get update \
	&& apt-get install -y \
		xfonts-base \
		xfonts-75dpi \
		xserver-common \
	&& curl -SLO "http://ftp.br.debian.org/debian/pool/main/g/glibc/libc6_2.29-6_amd64.deb" \
	&& dpkg -i libc6_2.29-6_amd64.deb \
	&& curl -SLO "http://security-cdn.debian.org/debian-security/pool/updates/main/o/openssl/libssl1.1_1.1.1d-0+deb10u2_amd64.deb" \
	&& dpkg -i libssl1.1_1.1.1d-0+deb10u2_amd64.deb \
	&& curl -SLO "http://ftp.br.debian.org/debian/pool/main/libp/libpng/libpng12-0_1.2.50-2+deb8u3_amd64.deb" \
	&& dpkg -i libpng12-0_1.2.50-2+deb8u3_amd64.deb \
	&& curl -SLO "https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.2.1/wkhtmltox-0.12.2.1_linux-jessie-amd64.deb" \
	&& dpkg -i wkhtmltox-0.12.2.1_linux-jessie-amd64.deb

ENV NODE_PATH /usr/local/lib/node_modules

ADD server.js /opt/server.js
RUN npm install -g forever wkhtmltopdf

EXPOSE 5001
ENTRYPOINT ["forever"]
CMD ["/opt/server.js"]
