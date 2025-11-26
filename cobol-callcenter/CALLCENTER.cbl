      *> ******************************************************************
      *> SISTEMA DE CALL CENTER - EATSFOOD
      *> Versao: 1.0
      *> Data: 25/11/2025
      *> Descricao: Sistema de atendimento baseado em comandos
      *> ******************************************************************
       IDENTIFICATION DIVISION.
       PROGRAM-ID. CALLCENTER.
       AUTHOR. EATSFOOD-TEAM.
       
       ENVIRONMENT DIVISION.
       CONFIGURATION SECTION.
       INPUT-OUTPUT SECTION.
       FILE-CONTROL.
           SELECT PEDIDOS-FILE ASSIGN TO "pedidos.dat"
               ORGANIZATION IS LINE SEQUENTIAL
               ACCESS MODE IS SEQUENTIAL
               FILE STATUS IS WS-FILE-STATUS.
               
           SELECT CLIENTES-FILE ASSIGN TO "clientes.dat"
               ORGANIZATION IS LINE SEQUENTIAL
               ACCESS MODE IS SEQUENTIAL
               FILE STATUS IS WS-FILE-STATUS.
               
           SELECT RESTAURANTES-FILE ASSIGN TO "restaurantes.dat"
               ORGANIZATION IS LINE SEQUENTIAL
               ACCESS MODE IS SEQUENTIAL
               FILE STATUS IS WS-FILE-STATUS.
       
       DATA DIVISION.
       FILE SECTION.
       FD  PEDIDOS-FILE.
       01  PEDIDO-REGISTRO.
           05 PED-ID              PIC 9(8).
           05 PED-NUMERO          PIC X(20).
           05 PED-CLIENTE-ID      PIC 9(8).
           05 PED-RESTAURANTE     PIC X(100).
           05 PED-STATUS          PIC X(20).
           05 PED-VALOR           PIC 9(6)V99.
           05 PED-DATA            PIC X(10).
           05 PED-TELEFONE        PIC X(15).
           
       FD  CLIENTES-FILE.
       01  CLIENTE-REGISTRO.
           05 CLI-ID              PIC 9(8).
           05 CLI-NOME            PIC X(100).
           05 CLI-TELEFONE        PIC X(15).
           05 CLI-EMAIL           PIC X(100).
           05 CLI-ENDERECO        PIC X(200).
           05 CLI-CPF             PIC X(14).
           
       FD  RESTAURANTES-FILE.
       01  RESTAURANTE-REGISTRO.
           05 REST-ID             PIC 9(8).
           05 REST-NOME           PIC X(100).
           05 REST-CODIGO         PIC X(20).
           05 REST-TELEFONE       PIC X(15).
           05 REST-STATUS         PIC X(20).
       
       WORKING-STORAGE SECTION.
       01  WS-FILE-STATUS         PIC XX.
       01  WS-EOF                 PIC X VALUE 'N'.
       01  WS-COMANDO             PIC X(50).
       01  WS-PARAMETRO           PIC X(50).
       01  WS-OPCAO               PIC 9.
       01  WS-CONTINUAR           PIC X VALUE 'S'.
       01  WS-ENCONTRADO          PIC X VALUE 'N'.
       
       01  WS-CABECALHO.
           05 FILLER              PIC X(60) VALUE ALL "=".
           
       01  WS-TITULO.
           05 FILLER              PIC X(20) VALUE SPACES.
           05 FILLER              PIC X(40) 
              VALUE "SISTEMA CALL CENTER - EATSFOOD".
              
       01  WS-MENU.
           05 FILLER              PIC X(60) VALUE SPACES.
           05 FILLER              PIC X(60) VALUE 
              "COMANDOS DISPONIVEIS:".
           05 FILLER              PIC X(60) VALUE 
              "  1. PEDIDO [numero]    - Consultar pedido".
           05 FILLER              PIC X(60) VALUE 
              "  2. CLIENTE [telefone] - Consultar cliente".
           05 FILLER              PIC X(60) VALUE 
              "  3. RESTAURANTE [codigo] - Consultar restaurante".
           05 FILLER              PIC X(60) VALUE 
              "  4. STATUS [numero]    - Atualizar status pedido".
           05 FILLER              PIC X(60) VALUE 
              "  5. LISTAR PEDIDOS     - Listar pedidos ativos".
           05 FILLER              PIC X(60) VALUE 
              "  6. AJUDA              - Mostrar comandos".
           05 FILLER              PIC X(60) VALUE 
              "  0. SAIR               - Encerrar sistema".
              
       01  WS-MENU-REDEF REDEFINES WS-MENU.
           05 WS-MENU-LINHA       PIC X(60) OCCURS 9 TIMES.
       
       PROCEDURE DIVISION.
       MAIN-PROCEDURE.
           PERFORM INICIALIZAR
           PERFORM UNTIL WS-CONTINUAR = 'N'
               PERFORM LOOP-PRINCIPAL
           END-PERFORM
           PERFORM FINALIZAR
           STOP RUN.
       
       INICIALIZAR.
           DISPLAY WS-CABECALHO
           DISPLAY WS-TITULO
           DISPLAY WS-CABECALHO
           DISPLAY " "
           PERFORM MOSTRAR-MENU.
       
       LOOP-PRINCIPAL.
           DISPLAY " "
           DISPLAY "====================================="
           DISPLAY "Digite o comando (0-6): " WITH NO ADVANCING
           ACCEPT WS-OPCAO
           DISPLAY " "
           
           EVALUATE WS-OPCAO
               WHEN 1
                   PERFORM CONSULTAR-PEDIDO
               WHEN 2
                   PERFORM CONSULTAR-CLIENTE
               WHEN 3
                   PERFORM CONSULTAR-RESTAURANTE
               WHEN 4
                   PERFORM ATUALIZAR-STATUS
               WHEN 5
                   PERFORM LISTAR-PEDIDOS
               WHEN 6
                   PERFORM MOSTRAR-MENU
               WHEN 0
                   MOVE 'N' TO WS-CONTINUAR
                   DISPLAY "Encerrando sistema..."
               WHEN OTHER
                   DISPLAY "Comando invalido! Digite 6 para ajuda."
           END-EVALUATE.
       
       MOSTRAR-MENU.
           PERFORM VARYING WS-OPCAO FROM 1 BY 1 UNTIL WS-OPCAO > 9
               DISPLAY WS-MENU-LINHA(WS-OPCAO)
           END-PERFORM.
       
       CONSULTAR-PEDIDO.
           DISPLAY " "
           DISPLAY "Numero do pedido: "
           ACCEPT WS-PARAMETRO
           
           MOVE 'N' TO WS-ENCONTRADO
           MOVE 'N' TO WS-EOF
           
           OPEN INPUT PEDIDOS-FILE
           
           IF WS-FILE-STATUS NOT = "00"
               DISPLAY "Erro ao abrir arquivo de pedidos"
               DISPLAY "Criando arquivo de exemplo..."
               PERFORM CRIAR-DADOS-EXEMPLO
           ELSE
               PERFORM LER-PEDIDOS UNTIL WS-EOF = 'S'
               
               IF WS-ENCONTRADO = 'N'
                   DISPLAY "Pedido nao encontrado: " WS-PARAMETRO
               END-IF
           END-IF
           
           CLOSE PEDIDOS-FILE.
       
       LER-PEDIDOS.
           READ PEDIDOS-FILE
               AT END
                   MOVE 'S' TO WS-EOF
               NOT AT END
                   IF PED-NUMERO = WS-PARAMETRO
                       PERFORM EXIBIR-PEDIDO
                       MOVE 'S' TO WS-ENCONTRADO
                   END-IF
           END-READ.
       
       EXIBIR-PEDIDO.
           DISPLAY " "
           DISPLAY "========================================="
           DISPLAY "DADOS DO PEDIDO"
           DISPLAY "========================================="
           DISPLAY "ID: " PED-ID
           DISPLAY "Numero: " PED-NUMERO
           DISPLAY "Cliente ID: " PED-CLIENTE-ID
           DISPLAY "Restaurante: " PED-RESTAURANTE
           DISPLAY "Status: " PED-STATUS
           DISPLAY "Valor: R$ " PED-VALOR
           DISPLAY "Data: " PED-DATA
           DISPLAY "Telefone: " PED-TELEFONE
           DISPLAY "=========================================".
       
       CONSULTAR-CLIENTE.
           DISPLAY " "
           DISPLAY "Telefone do cliente: "
           ACCEPT WS-PARAMETRO
           
           MOVE 'N' TO WS-ENCONTRADO
           MOVE 'N' TO WS-EOF
           
           OPEN INPUT CLIENTES-FILE
           
           IF WS-FILE-STATUS NOT = "00"
               DISPLAY "Erro ao abrir arquivo de clientes"
           ELSE
               PERFORM LER-CLIENTES UNTIL WS-EOF = 'S'
               
               IF WS-ENCONTRADO = 'N'
                   DISPLAY "Cliente nao encontrado: " WS-PARAMETRO
               END-IF
           END-IF
           
           CLOSE CLIENTES-FILE.
       
       LER-CLIENTES.
           READ CLIENTES-FILE
               AT END
                   MOVE 'S' TO WS-EOF
               NOT AT END
                   IF CLI-TELEFONE = WS-PARAMETRO
                       PERFORM EXIBIR-CLIENTE
                       MOVE 'S' TO WS-ENCONTRADO
                   END-IF
           END-READ.
       
       EXIBIR-CLIENTE.
           DISPLAY " "
           DISPLAY "========================================="
           DISPLAY "DADOS DO CLIENTE"
           DISPLAY "========================================="
           DISPLAY "ID: " CLI-ID
           DISPLAY "Nome: " CLI-NOME
           DISPLAY "Telefone: " CLI-TELEFONE
           DISPLAY "Email: " CLI-EMAIL
           DISPLAY "Endereco: " CLI-ENDERECO
           DISPLAY "CPF: " CLI-CPF
           DISPLAY "=========================================".
       
       CONSULTAR-RESTAURANTE.
           DISPLAY " "
           DISPLAY "Codigo do restaurante: "
           ACCEPT WS-PARAMETRO
           
           MOVE 'N' TO WS-ENCONTRADO
           MOVE 'N' TO WS-EOF
           
           OPEN INPUT RESTAURANTES-FILE
           
           IF WS-FILE-STATUS NOT = "00"
               DISPLAY "Erro ao abrir arquivo de restaurantes"
           ELSE
               PERFORM LER-RESTAURANTES UNTIL WS-EOF = 'S'
               
               IF WS-ENCONTRADO = 'N'
                   DISPLAY "Restaurante nao encontrado: " WS-PARAMETRO
               END-IF
           END-IF
           
           CLOSE RESTAURANTES-FILE.
       
       LER-RESTAURANTES.
           READ RESTAURANTES-FILE
               AT END
                   MOVE 'S' TO WS-EOF
               NOT AT END
                   IF REST-CODIGO = WS-PARAMETRO
                       PERFORM EXIBIR-RESTAURANTE
                       MOVE 'S' TO WS-ENCONTRADO
                   END-IF
           END-READ.
       
       EXIBIR-RESTAURANTE.
           DISPLAY " "
           DISPLAY "========================================="
           DISPLAY "DADOS DO RESTAURANTE"
           DISPLAY "========================================="
           DISPLAY "ID: " REST-ID
           DISPLAY "Nome: " REST-NOME
           DISPLAY "Codigo: " REST-CODIGO
           DISPLAY "Telefone: " REST-TELEFONE
           DISPLAY "Status: " REST-STATUS
           DISPLAY "=========================================".
       
       ATUALIZAR-STATUS.
           DISPLAY " "
           DISPLAY "Numero do pedido: "
           ACCEPT WS-PARAMETRO
           DISPLAY "Novo status (CONFIRMADO/PREPARANDO/ENVIADO/ENTREGUE): "
           ACCEPT WS-COMANDO
           
           DISPLAY "Status do pedido " WS-PARAMETRO 
                   " atualizado para " WS-COMANDO
           DISPLAY "[Funcionalidade de atualizacao sera implementada]".
       
       LISTAR-PEDIDOS.
           DISPLAY " "
           DISPLAY "========================================="
           DISPLAY "PEDIDOS ATIVOS"
           DISPLAY "========================================="
           
           MOVE 'N' TO WS-EOF
           
           OPEN INPUT PEDIDOS-FILE
           
           IF WS-FILE-STATUS NOT = "00"
               DISPLAY "Erro ao abrir arquivo de pedidos"
           ELSE
               PERFORM LER-E-LISTAR UNTIL WS-EOF = 'S'
           END-IF
           
           CLOSE PEDIDOS-FILE.
       
       LER-E-LISTAR.
           READ PEDIDOS-FILE
               AT END
                   MOVE 'S' TO WS-EOF
               NOT AT END
                   IF PED-STATUS NOT = "ENTREGUE" AND 
                      PED-STATUS NOT = "CANCELADO"
                       DISPLAY PED-NUMERO " | " PED-RESTAURANTE 
                               " | " PED-STATUS " | R$ " PED-VALOR
                   END-IF
           END-READ.
       
       CRIAR-DADOS-EXEMPLO.
           OPEN OUTPUT PEDIDOS-FILE
           
           MOVE 00000001 TO PED-ID
           MOVE "PED001" TO PED-NUMERO
           MOVE 00000100 TO PED-CLIENTE-ID
           MOVE "Restaurante Exemplo" TO PED-RESTAURANTE
           MOVE "PREPARANDO" TO PED-STATUS
           MOVE 104.29 TO PED-VALOR
           MOVE "25/11/2025" TO PED-DATA
           MOVE "11987654321" TO PED-TELEFONE
           WRITE PEDIDO-REGISTRO
           
           MOVE 00000002 TO PED-ID
           MOVE "PED002" TO PED-NUMERO
           MOVE 00000101 TO PED-CLIENTE-ID
           MOVE "Pizza da Hora" TO PED-RESTAURANTE
           MOVE "ENVIADO" TO PED-STATUS
           MOVE 85.50 TO PED-VALOR
           MOVE "25/11/2025" TO PED-DATA
           MOVE "11999887766" TO PED-TELEFONE
           WRITE PEDIDO-REGISTRO
           
           CLOSE PEDIDOS-FILE
           DISPLAY "Arquivo de exemplo criado com sucesso!".
       
       FINALIZAR.
           DISPLAY " "
           DISPLAY "Sistema encerrado. Ate logo!"
           DISPLAY WS-CABECALHO.
       
       END PROGRAM CALLCENTER.
