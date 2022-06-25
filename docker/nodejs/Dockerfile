FROM node:10

WORKDIR /var/www/lalegionduphenix_V2022/front

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["node", "server.js"]