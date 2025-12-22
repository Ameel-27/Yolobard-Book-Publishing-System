document.addEventListener('DOMContentLoaded', () => {

  const manuscripts = Array.from(document.querySelectorAll('.manuscript'));

  const titleInput = document.getElementById('search-title');
  const authorInput = document.getElementById('search-author');
  const genreSelect = document.getElementById('search-genre');
  const statusSelect = document.getElementById('search-status');
  const sortSelect = document.getElementById('sort-by');

  const searchBtn = document.getElementById('search-button');
  const resetBtn = document.getElementById('reset-button');

  function applyFilters() {
    const title = titleInput.value.trim().toLowerCase();
    const author = authorInput.value.trim().toLowerCase();
    const genre = genreSelect.value.toLowerCase();
    const status = statusSelect.value.toLowerCase();

    manuscripts.forEach(card => {
      const matchesTitle = card.dataset.title.includes(title);
      const matchesAuthor = card.dataset.author.includes(author);
      const matchesGenre = !genre || card.dataset.genre === genre;
      const matchesStatus = !status || card.dataset.status === status;

      if (matchesTitle && matchesAuthor && matchesGenre && matchesStatus) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }

    });

    applySort(); // Apply sorting after filtering
  }

  function applySort() {
    const sortBy = sortSelect.value;
    const wrapper = document.getElementById('manuscripts');
    const visible = manuscripts.filter(card => card.style.display !== 'none');

    visible.sort((a, b) => {
      switch (sortBy) {
        case 'newest':
          return new Date(b.dataset.date) - new Date(a.dataset.date);
        case 'oldest':
          return new Date(a.dataset.date) - new Date(b.dataset.date);
        case 'title':
          return a.dataset.title.localeCompare(b.dataset.title);
        case 'author':
          return a.dataset.author.localeCompare(b.dataset.author);
        default:
          return 0;
      }
    });

    // Append sorted elements
    visible.forEach(card => wrapper.appendChild(card));
  }

  // Event Listeners
  searchBtn.addEventListener('click', applyFilters);
  resetBtn.addEventListener('click', () => {
    titleInput.value = '';
    authorInput.value = '';
    genreSelect.value = '';
    statusSelect.value = '';
    sortSelect.value = 'newest';

    applySort();
  });

  sortSelect.addEventListener('change', applySort);
});
