/* ================================
   INLIB - SAMPLE DATA
   ================================ */

// Categories
const categories = [
  { id: 1, name: 'Engenharia de Software', count: 24, icon: '<i class="fas fa-code"></i>' },
  { id: 2, name: 'Ciência da Computação', count: 18, icon: '<i class="fas fa-microchip"></i>' },
  { id: 3, name: 'Matemática', count: 32, icon: '<i class="fas fa-square-root-alt"></i>' },
  { id: 4, name: 'Física', count: 15, icon: '<i class="fas fa-atom"></i>' },
  { id: 5, name: 'Biologia', count: 28, icon: '<i class="fas fa-dna"></i>' },
  { id: 6, name: 'Química', count: 22, icon: '<i class="fas fa-flask"></i>' },
  { id: 7, name: 'Administração', count: 16, icon: '<i class="fas fa-briefcase"></i>' },
  { id: 8, name: 'Economia', count: 20, icon: '<i class="fas fa-chart-line"></i>' },
  { id: 9, name: 'Monografias', count: 42, icon: '<i class="fas fa-book-open"></i>' }
];

// Books
const books = [
  {
    id: 1,
    title: 'Clean Code: A Handbook of Agile Software Craftsmanship',
    author: 'Robert C. Martin',
    category: 'Engenharia de Software',
    year: 2008,
    description: 'Guia essencial para escrever código limpo e profissional. Cobre princípios, padrões e práticas recomendadas para desenvolvimento de software.',
    pages: 464,
    language: 'Inglês',
    isbn: '978-0132350884',
    publisher: 'Prentice Hall',
    featured: true,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%230066cc%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EClean Code%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/clean_code.pdf'
  },
  {
    id: 2,
    title: 'Introduction to Algorithms',
    author: 'Thomas Cormen et al.',
    category: 'Ciência da Computação',
    year: 2009,
    description: 'Obra fundamental que aborda algoritmos computacionais, estruturas de dados e análise de complexidade.',
    pages: 1312,
    language: 'Inglês',
    isbn: '978-0262033848',
    publisher: 'MIT Press',
    featured: true,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23333333%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23ff9900%22%3EIntroduction to%3C/text%3E%3Ctext x=%2210%22 y=%2265%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23ff9900%22%3EAlgorithms%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/algorithms.pdf'
  },
  {
    id: 3,
    title: 'Cálculo Diferencial e Integral',
    author: 'James Stewart',
    category: 'Matemática',
    year: 2012,
    description: 'Texto abrangente cobrindo cálculo diferencial, integral, equações diferenciais e aplicações práticas.',
    pages: 1368,
    language: 'Português',
    isbn: '978-8522101032',
    publisher: 'Cengage Learning',
    featured: true,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23006600%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3ECálculo%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/calculo.pdf'
  },
  {
    id: 4,
    title: 'Física Moderna',
    author: 'Paul Tipler',
    category: 'Física',
    year: 2014,
    description: 'Introdução à física quântica, relatividade e partículas elementares. Esencial para entender fenômenos microscópicos.',
    pages: 656,
    language: 'Português',
    isbn: '978-8521623405',
    publisher: 'LTC Editora',
    featured: false,
    recent: true,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23cc0000%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EFísica%3C/text%3E%3Ctext x=%2210%22 y=%2270%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EModerna%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/fisica_moderna.pdf'
  },
  {
    id: 5,
    title: 'Biologia Molecular da Célula',
    author: 'Bruce Alberts',
    category: 'Biologia',
    year: 2015,
    description: 'Compreensão profunda dos mecanismos moleculares que controlam a vida celular e processos biológicos.',
    pages: 1464,
    language: 'Português',
    isbn: '978-8536328782',
    publisher: 'Artmed',
    featured: false,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23009933%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EBiologia%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/biologia_molecular.pdf'
  },
  {
    id: 6,
    title: 'Química Orgânica',
    author: 'Jonathan Clayden',
    category: 'Química',
    year: 2012,
    description: 'Estudo aprofundado de estruturas, reações e mecanismos de compostos orgânicos.',
    pages: 1240,
    language: 'Português',
    isbn: '978-8536321349',
    publisher: 'Artmed',
    featured: false,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23993300%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EQuímica%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/quimica_organica.pdf'
  },
  {
    id: 7,
    title: 'Administração Estratégica',
    author: 'Michael Porter',
    category: 'Administração',
    year: 2008,
    description: 'Análise estratégica de empresas, vantagem competitiva e formulação de estratégias corporativas.',
    pages: 512,
    language: 'Português',
    isbn: '978-8535211818',
    publisher: 'Campus',
    featured: false,
    recent: true,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23333366%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EAdministração%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/admin_estrategica.pdf'
  },
  {
    id: 8,
    title: 'Microeconomia: Princípios e Aplicações',
    author: 'Robert Pindyck',
    category: 'Economia',
    year: 2013,
    description: 'Fundamentos de microeconomia, comportamento do consumidor, teoria da produção e estruturas de mercado.',
    pages: 656,
    language: 'Português',
    isbn: '978-8535260282',
    publisher: 'Pearson',
    featured: true,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23663333%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23ffd700%22%3EMicroeconomia%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/microeconomia.pdf'
  },
  {
    id: 9,
    title: 'Design de Interfaces Web: UX/UI',
    author: 'Francisco Inchauste',
    category: 'Engenharia de Software',
    year: 2019,
    description: 'Princípios de design, usabilidade e experiência do usuário para aplicações web modernas.',
    pages: 384,
    language: 'Português',
    isbn: '978-8595157025',
    publisher: 'Novatec',
    featured: false,
    recent: true,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%239966cc%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EDesign UI/UX%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/design_ui.pdf'
  },
  {
    id: 10,
    title: 'Estruturas de Dados com Python',
    author: 'Magnus Lie Hetland',
    category: 'Ciência da Computação',
    year: 2010,
    description: 'Implementação e análise de estruturas de dados fundamentais usando Python.',
    pages: 528,
    language: 'Português',
    isbn: '978-8535214871',
    publisher: 'Novatec',
    featured: false,
    recent: false,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23336699%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EEstrutura de%3C/text%3E%3Ctext x=%2210%22 y=%2265%22 font-size=%2214%22 font-weight=%22bold%22 fill=%22%23fff%22%3EDados%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/estruturas_dados.pdf'
  },
  {
    id: 11,
    title: 'Monografia: Análise de Segurança em APIs',
    author: 'João Pedro da Silva',
    category: 'Monografias',
    year: 2023,
    description: 'Estudo aprofundado sobre vulnerabilidades em APIs RESTful e mecanismos de proteção implementados.',
    pages: 95,
    language: 'Português',
    isbn: '978-9999999999',
    publisher: 'Universidade Federal',
    featured: true,
    recent: true,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23444444%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2212%22 font-weight=%22bold%22 fill=%22%23fff%22%3EMonografia%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/mono_security.pdf'
  },
  {
    id: 12,
    title: 'Monografia: Machine Learning em Diagnóstico Médico',
    author: 'Maria Oliveira Santos',
    category: 'Monografias',
    year: 2023,
    description: 'Aplicação de algoritmos de machine learning para diagnóstico de doenças através de análise de imagens médicas.',
    pages: 112,
    language: 'Português',
    isbn: '978-9999999998',
    publisher: 'Universidade Federal',
    featured: false,
    recent: true,
    cover: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23555555%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2210%22 y=%2240%22 font-size=%2212%22 font-weight=%22bold%22 fill=%22%23fff%22%3EMonografia%3C/text%3E%3C/svg%3E',
    pdf: '/assets/pdf/mono_ml.pdf'
  }
];

// Users
const users = [
  { id: 1, name: 'João Silva', email: 'joao@inlib.com', type: 'Administrador', status: 'Ativo' },
  { id: 2, name: 'Maria Santos', email: 'maria@inlib.com', type: 'Usuário', status: 'Ativo' },
  { id: 3, name: 'Pedro Oliveira', email: 'pedro@inlib.com', type: 'Usuário', status: 'Inativo' },
  { id: 4, name: 'Ana Costa', email: 'ana@inlib.com', type: 'Editor', status: 'Ativo' }
];

// Admin
const adminUser = {
  name: 'João Silva',
  email: 'joao@inlib.com',
  role: 'Administrador',
  lastLogin: '2024-01-15 14:30',
  avatar: '<i class="fas fa-user-circle"></i>'
};

// Stats
const stats = {
  totalBooks: books.length,
  totalCategories: categories.length,
  totalUsers: users.length,
  totalPDFs: 12,
  loans: 127,
  booksFeatured: books.filter(b => b.featured).length,
  booksRecent: books.filter(b => b.recent).length,
  monographs: books.filter(b => b.category === 'Monografias').length
};

// Export functions for getting filtered data
function getBooks(category = null) {
  if (!category) return books;
  return books.filter(book => book.category === category);
}

function getBook(id) {
  return books.find(book => book.id === id);
}

function getFeaturedBooks() {
  return books.filter(book => book.featured);
}

function getRecentBooks() {
  return books.filter(book => book.recent);
}

function getCategories() {
  return categories;
}

function getCategory(id) {
  return categories.find(cat => cat.id === id);
}

function searchBooks(query) {
  const q = query.toLowerCase();
  return books.filter(book => 
    book.title.toLowerCase().includes(q) ||
    book.author.toLowerCase().includes(q) ||
    book.description.toLowerCase().includes(q)
  );
}

function getStats() {
  return stats;
}

function getUsers() {
  return users;
}

function getUser(id) {
  return users.find(user => user.id === id);
}

function getAdminUser() {
  return adminUser;
}
