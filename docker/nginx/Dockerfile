FROM node:lts-alpine as builder

ARG BUILD_SCRIPT="build"

COPY . /app
WORKDIR /app

RUN CI=1 npm install --force
RUN npm run-script ${BUILD_SCRIPT}

FROM nginx:1-alpine

ARG PHP_FPM_BACKEND="127.0.0.1:9000"

COPY ./public /app/public
COPY --from=builder /app/public/build /app/public

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/
RUN sed -i "s/\%PHP_FPM_BACKEND\%/${PHP_FPM_BACKEND}/g" /etc/nginx/conf.d/default.conf
