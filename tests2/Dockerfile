FROM vutreras/node_with_chrome
RUN mkdir /app
COPY . /app
WORKDIR /app
RUN rm -rf node_modules
RUN npm install
ENTRYPOINT ["sh", "-c", "npm run test"]