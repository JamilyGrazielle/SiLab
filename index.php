<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="Estilo/estilo.css" />
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
    <title>Página Principal - SiLab</title>
  </head>
  <body id="pagina-principal">
    
    <?php require_once 'includes/header-principal.php';?>

    <main id="principal01">
      <section id="slideshow_section">
        <div class="slides">
          <img src="Imagens/img_laboratorio.png" alt="Jovem Estudando" />
          <div class="slide_caption">
            <h2>Otimize a gestão dos seus Laboratórios de Informática</h2>
            <p>
              Uma plataforma centralizada para agendamento de aulas, reservas
              para estudo e controle de uso, facilitando o dia a dia de todos.
            </p>
          </div>
        </div>
        <button class="prev" onclick="mudarSlides(-1)">&#10094;</button>
        <button class="next" onclick="mudarSlides(1)">&#10095;</button>
      </section>

      <h2 style="color: #2D3748; text-align: center;">Principais Funcionalidades</h2>

      <section id="galeria">
        
        <div class="conteudo_galeria">
            <i class="fa-solid fa-calendar"></i>
            <div class="desc_galeria">
                <h3>Agenda Centralizada</h3>
                <p>Visualize todos os agendamentos em um único lugar, evitando conflitos de horário.</p>
            </div>
        </div>
      
        <div class="conteudo_galeria">
            <i class="fas fa-file-alt"></i>
            <div class="desc_galeria">
                <h3>Reservas Online Fáceis</h3>
                <p>Professores e alunos podem reservar laboratórios de forma rápida e intuitiva.</p>
            </div>
        </div>

        <div class="conteudo_galeria">
            <i class="fas fa-pie-chart"></i>
            <div class="desc_galeria">
                <h3>Relatórios de Uso</h3>
                <p>Entenda como os laboratórios são utilizados para otimizar recursos.</p>
            </div>
        </div>
      </section>
    </main>

    <?php require_once 'includes/footer.php';?>

    <script>
      let slideIndex = 0;
      mostrarSlides(slideIndex);

      function mudarSlides(n) {
        mostrarSlides((slideIndex += n));
      }

      function mostrarSlides(n) {
        let slides = document.getElementsByClassName("slides");

        if (n > slides.length) {
          slideIndex = 1;
        }

        if (n < 1) {
          slideIndex = slides.length;
        }

        for (let i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
        }

        slides[slideIndex - 1].style.display = "block";
      }

      setInterval(() => mudarSlides(1), 8000);
    </script>
  </body>
</html>
