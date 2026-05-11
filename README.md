# Documentação do Sistema de Gestão de Ativos

## 1. Domínio e justificativa

- **Domínio:** sistema de gestão de ativos de TI / patrimônio.
- **Descrição:** O sistema cadastra, lista, filtra e exporta relatórios de ativos como servidores, notebooks, impressoras e outros equipamentos utilizados por uma organização.
- **Justificativa:**
  - Empresas e setores de TI precisam manter controle sobre seus ativos para evitar perdas e desperdícios.
  - Permite localizar rapidamente cada equipamento, saber quem é o responsável e qual o status do ativo.
  - Facilita a manutenção, a auditoria e a gestão do inventário.
- **Usuários reais:**
  - gestor de TI
  - administrador de patrimônio
  - técnico de manutenção
  - auditor interno
  - gerente financeiro

## 2. Protótipo de telas

As telas principais do sistema são:

1. **Tela de login**
   - Campos: usuário, senha.
   - Botão: Entrar.
   - Mensagem de erro em caso de login inválido.

2. **Tela de listagem de ativos**
   - Tabela com colunas: ID, ativo, tipo, responsável, departamento, status e ações.
   - Filtros no topo: busca por nome, tipo e responsável.
   - Botões: cadastrar novo ativo, exportar PDF, editar, excluir.

3. **Tela de cadastro/edição de ativo**
   - Campos: nome do ativo, tipo, número de patrimônio, data de aquisição, responsável, departamento, status, observações.
   - Botões: salvar e cancelar.

4. **Tela de relatório em PDF**
   - Filtros de relatório: tipo, responsável e período.
   - Botão: Gerar PDF.
   - Exibição resumida dos dados antes da geração.

> O protótipo pode ser desenhado no Figma, no papel fotografado ou em qualquer ferramenta simples. O essencial é mostrar a sequência: login → listagem → cadastro/edição → relatório.

## 3. Modelo Entidade-Relacionamento (MER)

### Tabelas principais

- `usuarios`
  - `id_usuario` PK
  - `nome`
  - `email`
  - `senha`
  - `perfil`

- `ativos`
  - `id_ativo` PK
  - `nome_ativo`
  - `tipo`
  - `numero_patrimonio`
  - `data_aquisicao`
  - `fk_responsavel` FK → `funcionarios.id_funcionario`
  - `fk_departamento` FK → `departamentos.id_departamento`
  - `fk_categoria` FK → `categorias.id_categoria`
  - `status`
  - `observacoes`

- `funcionarios`
  - `id_funcionario` PK
  - `nome`
  - `email`
  - `telefone`

- `departamentos`
  - `id_departamento` PK
  - `nome_departamento`

- `categorias`
  - `id_categoria` PK
  - `nome_categoria`
  - `descricao`

- `manutencoes`
  - `id_manutencao` PK
  - `fk_ativo` FK → `ativos.id_ativo`
  - `data_manutencao`
  - `tipo_manutencao`
  - `responsavel_manutencao`
  - `observacoes`

### Relacionamentos

- Um `ativo` tem um `responsável` em `funcionarios`.
- Um `ativo` pertence a um `departamento`.
- Um `ativo` pertence a uma `categoria`.
- Uma `manutencao` referencia um `ativo`.
- Usuários (`usuarios`) controlam o acesso ao sistema.

## 4. Dicionário de banco de dados

| Tabela | Campo | Tipo PostgreSQL | Restrição | Descrição |
|---|---|---|---|---|
| `usuarios` | `id_usuario` | SERIAL | PK, NOT NULL | Identificador do usuário |
|  | `nome` | VARCHAR(100) | NOT NULL | Nome completo do usuário |
|  | `email` | VARCHAR(150) | NOT NULL, UNIQUE | E-mail de login |
|  | `senha` | VARCHAR(255) | NOT NULL | Hash da senha |
|  | `perfil` | VARCHAR(20) | NOT NULL | Perfil de acesso (admin, tecnico) |
| `ativos` | `id_ativo` | SERIAL | PK, NOT NULL | Identificador do ativo |
|  | `nome_ativo` | VARCHAR(150) | NOT NULL | Nome do equipamento |
|  | `tipo` | VARCHAR(50) | NOT NULL | Tipo do ativo |
|  | `numero_patrimonio` | VARCHAR(50) | UNIQUE | Número de patrimônio |
|  | `data_aquisicao` | DATE | NOT NULL | Data de aquisição |
|  | `fk_responsavel` | INTEGER | NOT NULL, FK | Funcionário responsável |
|  | `fk_departamento` | INTEGER | NOT NULL, FK | Departamento do ativo |
|  | `fk_categoria` | INTEGER | NOT NULL, FK | Categoria do ativo |
|  | `status` | VARCHAR(30) | NOT NULL | Estado atual do ativo |
|  | `observacoes` | TEXT | NULL | Observações adicionais |
| `funcionarios` | `id_funcionario` | SERIAL | PK, NOT NULL | Identificador do funcionário |
|  | `nome` | VARCHAR(100) | NOT NULL | Nome do funcionário |
|  | `email` | VARCHAR(150) | NULL | E-mail do funcionário |
|  | `telefone` | VARCHAR(20) | NULL | Telefone do funcionário |
| `departamentos` | `id_departamento` | SERIAL | PK, NOT NULL | Identificador do departamento |
|  | `nome_departamento` | VARCHAR(100) | NOT NULL | Nome do departamento |
| `categorias` | `id_categoria` | SERIAL | PK, NOT NULL | Identificador da categoria |
|  | `nome_categoria` | VARCHAR(100) | NOT NULL | Nome da categoria |
|  | `descricao` | TEXT | NULL | Descrição da categoria |
| `manutencoes` | `id_manutencao` | SERIAL | PK, NOT NULL | Identificador do registro de manutenção |
|  | `fk_ativo` | INTEGER | NOT NULL, FK | Ativo mantido |
|  | `data_manutencao` | DATE | NOT NULL | Data da manutenção |
|  | `tipo_manutencao` | VARCHAR(50) | NOT NULL | Tipo de serviço realizado |
|  | `responsavel_manutencao` | VARCHAR(100) | NOT NULL | Responsável pela manutenção |
|  | `observacoes` | TEXT | NULL | Detalhes da manutenção |

## 5. Definição dos filtros de busca

Filtros obrigatórios:

1. **Nome do ativo**
   - Tipo: texto livre
   - Campo: `nome_ativo`
   - Uso: pesquisa parcial por nome ou parte do nome do equipamento.

2. **Tipo de ativo**
   - Tipo: seleção por categoria
   - Campo: `tipo`
   - Uso: filtrar ativos por categoria, como Servidor, Notebook, Impressora.

3. **Responsável**
   - Tipo: seleção por lista
   - Campo: `fk_responsavel`
   - Uso: filtrar os ativos atribuídos a um funcionário específico.

> Esses filtros permitem ao usuário encontrar rapidamente o ativo desejado e gerar relatórios mais precisos.

## 6. Descrição do relatório em PDF

- O relatório consolida a lista de ativos com base nos filtros aplicados.
- Deve incluir os seguintes dados:
  - ID do ativo
  - Nome do ativo
  - Tipo
  - Responsável
  - Departamento
  - Número de patrimônio
  - Data de aquisição
  - Status
- **Utilidade prática:**
  - gera inventário formal para auditoria e controle;
  - permite compartilhar informações com a gerência;
  - facilita conferência em inventários físicos;
  - oferece visão rápida de quais ativos estão em uso ou disponíveis.

