<script>
  function toggleDropdown(event, dropdownId) {
    event.stopPropagation();
    const dropdown = document.getElementById(dropdownId);

    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
      if (d.id !== dropdownId) d.classList.add('hidden');
    });

    dropdown?.classList.toggle('hidden');
  }

  document.addEventListener('click', () => {
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
  });
</script>
