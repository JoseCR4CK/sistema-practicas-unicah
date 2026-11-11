BLOQUE 5 AUTÓNOMO — INTEGRACIÓN, SEGURIDAD, LIMPIEZA Y PREPARACIÓN DE ENTREGA

Este bloque debe comenzar automáticamente cuando el Bloque 4 esté terminado y sus pruebas pasen.

No solicites autorización intermedia para acciones ya autorizadas.

Mantén todas las restricciones del Bloque 4.

Trabaja únicamente dentro de:

C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah

Base autorizada:

sistema_practicas_unicah

NO hagas push.

────────────────────
1. AUDITORÍA DE FUENTES DE DATOS
────────────────────

Revisa todo el proyecto y detecta módulos que todavía utilicen:

- arreglos estáticos;
- IDs demo;
- datos hardcodeados;
- localStorage;
- stores JavaScript;

para información académica que ya tenga backend persistente.

No elimines nada automáticamente sin identificar consumidores.

Para cada store:

1. localizar consumidores;
2. comprobar si existe backend equivalente;
3. migrar consumidores;
4. validar;
5. eliminar únicamente si ya no existe dependencia.

Revisa especialmente:

practices-store.js

Si todavía existe un consumidor válido fuera de los módulos migrados, consérvalo y repórtalo.

NO migres ni elimines el almacenamiento privado de:

- Agenda;
- Mis notas;
- Mis pendientes;
- Bitácora.

Esas herramientas siguen siendo privadas del estudiante.

────────────────────
2. INTEGRACIÓN END-TO-END
────────────────────

Audita que los portales compartan la misma información persistente.

COORDINACIÓN
- estudiantes;
- empresas;
- docentes;
- prácticas;
- solicitudes;
- documentos;
- visitas;
- monografías;
- ternas;
- defensas.

DOCENTE
- practicantes asignados;
- visitas;
- seguimientos;
- revisión técnica de monografía;
- ternas donde participa;
- defensas donde participa;
- carga académica.

ESTUDIANTE
- su práctica;
- sus solicitudes;
- sus documentos;
- sus visitas;
- su monografía y versiones;
- su terna;
- su defensa.

Una modificación persistente autorizada debe reflejarse en los otros portales sin copias paralelas.

────────────────────
3. AUTORIZACIÓN POR REGISTRO
────────────────────

Haz una auditoría completa de seguridad.

No basta con middleware por rol.

Comprueba que:

ESTUDIANTE:
- nunca consulta registros de otro estudiante mediante URL manual.

DOCENTE:
- solo consulta practicantes asignados;
- solo registra revisiones técnicas que le correspondan;
- solo consulta ternas donde participa;
- solo consulta defensas donde participa.

JEFE_INMEDIATO:
- no obtiene acceso académico adicional por accidente.

COORDINACION:
- mantiene gestión académica autorizada.

ROOT:
- conserva el comportamiento existente.

Corrige cualquier IDOR o acceso cruzado encontrado.

────────────────────
4. VALIDACIÓN Y SEGURIDAD
────────────────────

Audita:

- Form Requests o validaciones equivalentes;
- mass assignment;
- relaciones padre/hijo;
- IDs manipulados;
- CSRF;
- acciones repetidas;
- estados incompatibles;
- consultas de registros inexistentes;
- respuestas 403/404 adecuadas.

No introduzcas una arquitectura innecesariamente compleja.

Corrige únicamente problemas reales encontrados.

────────────────────
5. DATOS Y SEEDERS
────────────────────

Revisa todos los seeders del proyecto.

Deben ser:

- coherentes;
- idempotentes;
- seguros al reejecutarse;
- sin sobrescribir ediciones manuales innecesariamente.

No dejes textos visibles como:

“demo”
“prueba”
“para demostrar”

Sustitúyelos por información neutra cuando sea necesario.

No inventes personas ni requisitos institucionales.

Reejecuta los seeders pertinentes y confirma que no generan duplicados.

────────────────────
6. COHERENCIA VISUAL
────────────────────

No rediseñes el proyecto.

Corrige únicamente inconsistencias claras:

- nombre/cuenta pegados;
- botones incorrectos según rol;
- textos dirigidos al rol equivocado;
- contadores ambiguos;
- etiquetas de estado incoherentes;
- enlaces con IDs simulados;
- mensajes internos de desarrollo;
- formularios que ofrecen acciones que el usuario no puede realizar.

Mantén la identidad visual existente.

────────────────────
7. REPORTES Y DASHBOARDS
────────────────────

Audita que los contadores visibles provengan de MySQL/Eloquent cuando el módulo ya esté migrado.

No inventes métricas nuevas.

Corrige únicamente contadores existentes que todavía sean:

- fijos;
- demo;
- provenientes de stores eliminados;
- incoherentes con los listados.

El informe existente de estudiantes debe seguir usando datos reales.

────────────────────
8. FINALIZACIÓN
────────────────────

NO implementes una regla automática definitiva de Finalización.

Todavía están pendientes las condiciones institucionales exactas.

Puedes:

- mostrar el estado existente;
- reportar requisitos pendientes conocidos;
- mantener estructura preparada.

NO:

- cerrar automáticamente una práctica;
- aprobarla por porcentaje;
- asumir que horas + documentos son suficientes.

────────────────────
9. REGRESIONES Y PRUEBAS
────────────────────

Amplía las pruebas de integración para cubrir los flujos existentes.

Como mínimo verifica:

- login por rol;
- logout;
- acceso 403;
- registros inexistentes 404;
- estudiante aislado de otros estudiantes;
- docente aislado de otros docentes;
- persistencia de Bloques 1–4;
- relaciones práctica-estudiante-docente-empresa;
- Solicitudes;
- Documentos;
- Visitas;
- Seguimientos;
- Monografía;
- Terna;
- Defensa;
- Carga académica;
- disponibilidad.

Ejecuta:

php artisan migrate:status

php artisan test --do-not-cache-result

php artisan route:list

git diff --check

Realiza también comprobaciones GET sobre MySQL cuando sea útil.

Si encuentras un fallo:

- identifica causa;
- corrige;
- vuelve a ejecutar pruebas;
- no ignores errores.

────────────────────
10. REGLAS INSTITUCIONALES PENDIENTES
────────────────────

NO inventes:

- documentos específicos de Visita;
- evaluación formal del Jefe inmediato;
- requisitos definitivos para Defensa;
- responsable definitivo de estilo;
- identidad exacta del coordinador revisor;
- rúbrica/calificación de Defensa;
- reglas de ausencia/reprogramación;
- PERSONAL_CAMPUS;
- condiciones finales de cierre;
- equivalencia completa créditos/horas.

Cuando una regla pendiente impida completar una funcionalidad:

- deja la estructura segura;
- no inventes comportamiento;
- documenta claramente el pendiente;
- continúa con las demás tareas.

────────────────────
11. COMMITS
────────────────────

Realiza commits pequeños por grupo lógico.

No hagas push.

No hagas merge a main.

No borres ramas existentes.

Al final debe quedar un árbol limpio.

────────────────────
12. INFORME FINAL
────────────────────

Cuando termines TODO el Bloque 5, DETENTE.

Entrega un informe completo con:

1. trabajo realizado en Bloque 4;
2. trabajo realizado en Bloque 5;
3. migraciones creadas/aplicadas;
4. seeders ejecutados;
5. archivos creados;
6. archivos modificados;
7. stores eliminados;
8. stores conservados y motivo;
9. vulnerabilidades o problemas de autorización encontrados;
10. correcciones realizadas;
11. datos hardcodeados eliminados;
12. contadores corregidos;
13. módulos completamente persistentes;
14. módulos todavía dependientes de localStorage;
15. reglas institucionales pendientes;
16. pruebas ejecutadas;
17. número de pruebas y aserciones;
18. validaciones MySQL;
19. rutas registradas;
20. commits realizados;
21. `git status` final;
22. rama final;
23. confirmación de que NO hiciste push;
24. confirmación de que NO hiciste merge a main;
25. confirmación de que no accediste ni modificaste nada fuera del repositorio autorizado.

Después de entregar ese informe no realices más cambios.