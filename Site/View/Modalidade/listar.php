 <h2>Modalidades cadastradas</h2>
    <?php if (empty($modalidades)): ?>
        <p>Nenhuma modalidade cadastrada.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Máximo de participantes</th>
                    <th>Gênero</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modalidades as $modalidade): ?>
                    <tr>
                        <td><?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $modalidade['quantidade'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($modalidade['genero'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>