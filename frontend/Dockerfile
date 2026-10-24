FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 5173

# Menjalankan Vite dev server
CMD ["npm", "run", "dev", "--", "--host"]