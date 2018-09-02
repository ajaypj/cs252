/* Client code */
/* TODO : Modify to meet your need */
#include <stdio.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <netinet/in.h>

#define PORT_NUMBER     5432
#define SERVER_ADDRESS  "127.0.0.1"
#define FILEADDR        "received_files/"

int main(int argc, char **argv)
{
        int client_socket;
        ssize_t len;
        struct sockaddr_in remote_addr;
        char buffer[BUFSIZ];
        int file_size;
        FILE *received_file;
        int remain_data = 0;
		char name[50];
        /* Zeroing remote_addr struct */
        memset(&remote_addr, 0, sizeof(remote_addr));

        /* Construct remote_addr struct */
        remote_addr.sin_family = AF_INET;
        inet_pton(AF_INET, SERVER_ADDRESS, &(remote_addr.sin_addr));
        remote_addr.sin_port = htons(PORT_NUMBER);

        /* Create client socket */
        client_socket = socket(AF_INET, SOCK_STREAM, 0);
        if (client_socket == -1)
        {
                fprintf(stderr, "Error creating socket --> %s\n", strerror(errno));

                exit(EXIT_FAILURE);
        }

        /* Connect to the server */
        if (connect(client_socket, (struct sockaddr *)&remote_addr, sizeof(struct sockaddr)) == -1)
        {
                fprintf(stderr, "Error on connect --> %s\n", strerror(errno));

                exit(EXIT_FAILURE);
        }
		/*send query*/
		int cnt = 0;
		for(int i =1;i<argc;i++){
			strcpy(buffer,argv[i]);
			send(client_socket,buffer,32,0);
			printf("send data %s\n",buffer);
			if(argv[i][0]>='0'&&argv[i][0]<='9') cnt+=argv[i][0]-'0';
		}
		strcpy(buffer,"end");
		send(client_socket,buffer,32,0);
		printf("send data %s\n",buffer);
		for(int i =0;i<cnt;i++){
			/* Receiving file size */
			recv(client_socket, buffer, BUFSIZ, 0);
			file_size = atoi(buffer);
			fprintf(stdout, "\nFile size : %d\n", file_size);
			printf("GGGGGGGGGG");
			printf("%d\n",i);
			sprintf(name,"%d.jpg",i);
			char temp[60] = FILEADDR;
			strcat(temp,name);
			printf("%s\n",temp);
			received_file = fopen(temp, "w");
			if (received_file == NULL)
			{
					fprintf(stderr, "Failed to open file foo --> %s\n", strerror(errno));
					exit(EXIT_FAILURE);
			}
			remain_data = file_size;
			printf("xx\n");
			while ((remain_data > 0) && ((len = recv(client_socket, buffer, BUFSIZ, 0)) > 0))
			{
					fwrite(buffer, sizeof(char), len, received_file);
					remain_data -= len;
					fprintf(stdout, "Receive %d bytes and we hope :- %d bytes\n", len, remain_data);
			}
			strcpy(buffer,"end");
			send(client_socket,buffer,32,0);
			fprintf(stdout, "\nFile size : %d\n", len);
			fclose(received_file);
		}
		close(client_socket);

        return 0;
}
