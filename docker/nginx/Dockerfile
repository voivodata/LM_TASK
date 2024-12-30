FROM nginx:latest
RUN echo 'alias ll="ls -la"' >> ~/.bashrc
RUN apt-get update && apt-get install -y vim nginx
COPY ./default.conf /etc/nginx/conf.d