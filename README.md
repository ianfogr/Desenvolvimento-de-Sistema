# SafeAudit - Sistema de Gestão de Ativos

## ✅ Implementado

### 1. **Autenticação**
- Tela de login com validação de email e senha
- Sessão de usuário com 2 perfis: admin e técnico
- Botão de logout em todas as páginas protegidas
- Credenciais padrão:
  - Email: `admin@safeaudit.com`
  - Senha: `admin123`

### 2. **Banco de Dados Expandido**
Tabelas criadas:
- `usuarios` - Autenticação e controle de acesso
- `departamentos` - Departamentos da organização
- `categorias` - Categorias de ativos
- `funcionarios` - Funcionários responsáveis pelos ativos
- `ativos` - Ativos com todos os campos (expandido)
- `manutencoes` - Histórico de manutenções

### 3. **Interface Web (CRUD)**
- **index.php** - Listagem com filtros e logout
- **criar.php** - Cadastro de novos ativos
- **editar.php** - Edição de ativos
- **deletar.php** - Exclusão de ativos
- **gerar_pdf.php** - Exportar relatório em PDF
- **login.php** - Autenticação
- **logout.php** - Encerrar sessão

### 4. **API REST**

#### Endpoint: `/api/ativos.php`

**Autenticação**: Requer sessão ativa (faça login primeiro)

#### GET - Listar todos os ativos
```bash
curl -b "PHPSESSID=seu_session_id" http://localhost/trabalho/api/ativos.php
```

Resposta:
```json
[
  {
    "id_ativo": 1,
    "nome_ativo": "Servidor de produção",
    "tipo": "Servidor",
    "numero_patrimonio": "PAT-001",
    "data_aquisicao": "2023-01-15",
    "fk_responsavel": 1,
    "fk_departamento": 1,
    "fk_categoria": 1,
    "status": "ativo",
    "observacoes": "Servidor principal de produção",
    "responsavel": "Ana Silva",
    "nome_departamento": "TI",
    "nome_categoria": "Servidores"
  }
]
```

#### GET - Obter um ativo específico
```bash
curl -b "PHPSESSID=seu_session_id" http://localhost/trabalho/api/ativos.php?id=1
```

#### POST - Criar novo ativo
```bash
curl -b "PHPSESSID=seu_session_id" -X POST http://localhost/trabalho/api/ativos.php \
  -H "Content-Type: application/json" \
  -d '{
    "nome_ativo": "Novo Notebook",
    "tipo": "Notebook",
    "numero_patrimonio": "PAT-004",
    "data_aquisicao": "2024-01-10",
    "fk_responsavel": 2,
    "fk_departamento": 1,
    "fk_categoria": 2,
    "status": "ativo",
    "observacoes": "Novo equipamento"
  }'
```

Resposta:
```json
{"id_ativo": "4"}
```

#### PUT - Atualizar ativo
```bash
curl -b "PHPSESSID=seu_session_id" -X PUT http://localhost/trabalho/api/ativos.php?id=1 \
  -H "Content-Type: application/json" \
  -d '{
    "nome_ativo": "Servidor de produção (atualizado)",
    "status": "manutenção"
  }'
```

Resposta:
```json
{"mensagem": "Ativo atualizado com sucesso"}
```

#### DELETE - Excluir ativo
```bash
curl -b "PHPSESSID=seu_session_id" -X DELETE http://localhost/trabalho/api/ativos.php?id=1
```

Resposta:
```json
{"mensagem": "Ativo excluído com sucesso"}
```

---

## 🚀 Como Usar

### 1. Executar o banco de dados
```bash
mysql -u root -p < safeaudit_mysql.sql
```
(A senha padrão do XAMPP é vazia ou deixe em branco)

### 2. Iniciar XAMPP
- Inicie Apache e MySQL no painel do XAMPP

### 3. Acessar o sistema
- Abra: `http://localhost/trabalho/`
- Será redirecionado para o login
- Use as credenciais de teste

### 4. Usar a API (com ferramenta como Postman ou curl)
- Faça login primeiro pelo navegador
- Use o `PHPSESSID` da sua sessão nos requests da API

---

## 📊 Estrutura de Tabelas

### usuarios
```
id_usuario, nome, email, senha (bcrypt), perfil (admin/tecnico), data_criacao
```

### ativos (expandido)
```
id_ativo, nome_ativo, tipo, numero_patrimonio, data_aquisicao,
fk_responsavel, fk_departamento, fk_categoria, status, observacoes, data_criacao
```

### departamentos
```
id_departamento, nome_departamento
```

### categorias
```
id_categoria, nome_categoria, descricao
```

### funcionarios
```
id_funcionario, nome, email, telefone
```

### manutencoes
```
id_manutencao, fk_ativo, data_manutencao, tipo_manutencao, responsavel_manutencao, observacoes
```

---

## 🔐 Segurança

- ✅ Senhas armazenadas com bcrypt (password_verify)
- ✅ Validação de sessão em todas as páginas protegidas
- ✅ Prepared statements para prevenir SQL injection
- ✅ Validação de entrada com htmlspecialchars
- ✅ API com autenticação baseada em sessão

---

## 📝 Próximas Melhorias (Opcionais)

- Implementar geração de PDF nativa com FPDF/Dompdf
- Adicionar histórico de auditoria
- Adicionar filtros avançados na API
- Implementar paginação
- Adicionar validação de permissões (admin vs técnico)
- Dashboard com gráficos de ativos por departamento/categoria
