      *> ******************************************************************
      *> SISTEMA CALL CENTER EATSFOOD - Versao Simples
      *> ******************************************************************
       IDENTIFICATION DIVISION.
       PROGRAM-ID. CALLCENTER-SIMPLES.
       
       DATA DIVISION.
       WORKING-STORAGE SECTION.
       01  WS-OPCAO               PIC 9.
       01  WS-CONTINUAR           PIC X VALUE 'S'.
       01  WS-PARAMETRO           PIC X(50).
       
       PROCEDURE DIVISION.
       MAIN-PROCEDURE.
           DISPLAY "=========================================="
           DISPLAY "   SISTEMA CALL CENTER - EATSFOOD"
           DISPLAY "=========================================="
           DISPLAY " "
           
           PERFORM UNTIL WS-CONTINUAR = 'N'
               DISPLAY "MENU:"
               DISPLAY "  1 - Consultar Pedido"
               DISPLAY "  2 - Consultar Cliente"
               DISPLAY "  3 - Listar Pedidos"
               DISPLAY "  0 - Sair"
               DISPLAY " "
               DISPLAY "Digite sua opcao: " WITH NO ADVANCING
               ACCEPT WS-OPCAO
               DISPLAY " "
               
               IF WS-OPCAO = 1 THEN
                   DISPLAY "Digite o numero do pedido: " 
                       WITH NO ADVANCING
                   ACCEPT WS-PARAMETRO
                   DISPLAY "Consultando pedido: " WS-PARAMETRO
                   DISPLAY "[Funcionalidade em desenvolvimento]"
                   DISPLAY " "
               ELSE IF WS-OPCAO = 2 THEN
                   DISPLAY "Digite o telefone do cliente: " 
                       WITH NO ADVANCING
                   ACCEPT WS-PARAMETRO
                   DISPLAY "Consultando cliente: " WS-PARAMETRO
                   DISPLAY "[Funcionalidade em desenvolvimento]"
                   DISPLAY " "
               ELSE IF WS-OPCAO = 3 THEN
                   DISPLAY "PEDIDOS ATIVOS:"
                   DISPLAY "  PED001 - Pizza da Casa - R$ 45.00"
                   DISPLAY "  PED002 - Hamburguer - R$ 35.00"
                   DISPLAY "  PED003 - Sushi Combo - R$ 89.00"
                   DISPLAY " "
               ELSE IF WS-OPCAO = 0 THEN
                   MOVE 'N' TO WS-CONTINUAR
                   DISPLAY "Encerrando sistema..."
               ELSE
                   DISPLAY "Opcao invalida!"
                   DISPLAY " "
               END-IF
           END-PERFORM
           
           DISPLAY "=========================================="
           DISPLAY "Sistema encerrado. Ate logo!"
           DISPLAY "=========================================="
           STOP RUN.
       
       END PROGRAM CALLCENTER-SIMPLES.
