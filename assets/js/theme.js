document.documentElement.classList.add('jr-theme-foundation-ready');

document.addEventListener('DOMContentLoaded', function () {
  // Collapse an active video card and clear its iframe src.
  function collapseCard(card) {
    var thumb = card.querySelector('.video-card__thumb');
    var embed = card.querySelector('.video-card__embed');
    var iframe = embed ? embed.querySelector('iframe') : null;
    card.classList.remove('active');
    card.setAttribute('aria-expanded', 'false');
    if (thumb) thumb.removeAttribute('hidden');
    if (embed) embed.setAttribute('hidden', '');
    if (iframe) iframe.src = 'about:blank';
  }

  // Expand a video card and load the YouTube embed.
  function expandCard(card) {
    var videoId = card.dataset.videoId;
    if (!videoId) return;
    // Only accept well-formed YouTube IDs (11 alphanumeric/dash/underscore chars).
    if (!/^[A-Za-z0-9_-]{11}$/.test(videoId)) return;
    var thumb = card.querySelector('.video-card__thumb');
    var embed = card.querySelector('.video-card__embed');
    var iframe = embed ? embed.querySelector('iframe') : null;
    if (!embed || !iframe) return;
    iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1';
    if (thumb) thumb.setAttribute('hidden', '');
    embed.removeAttribute('hidden');
    card.classList.add('active');
    card.setAttribute('aria-expanded', 'true');
  }

  // Click handler — collapse active card first, then expand clicked one if different.
  document.querySelectorAll('.video-strip').forEach(function (strip) {
    strip.addEventListener('click', function (e) {
      var card = e.target.closest('.video-card');
      if (!card) return;

      // Prevent clicks inside the embed (iframe, watch link) from re-triggering.
      if (e.target.closest('.video-card__embed')) return;

      var wasActive = card.classList.contains('active');

      // Collapse all active cards across all strips.
      document.querySelectorAll('.video-card.active').forEach(collapseCard);

      if (!wasActive) {
        expandCard(card);
      }
    });
  });

  // Keyboard support: Enter or Space activates a card.
  document.querySelectorAll('.video-strip').forEach(function (strip) {
    strip.addEventListener('keydown', function (e) {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      if (e.target.closest('.video-card__embed')) return;
      var card = e.target.closest('.video-card');
      if (!card) return;
      e.preventDefault();
      card.click();
    });
  });
});
