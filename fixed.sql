# создаем индексы для полей, используемых в выборках, сортировках и т.д.
CREATE INDEX idx_author_name ON author (first_name, last_name);

# избавляемся от вложенных запросов
SELECT author.id, author.first_name, author.last_name, COUNT(book.id) AS book_count
FROM author
         JOIN book ON author.id = book.author_id
WHERE author.first_name = 'Zaria' AND author.last_name = 'Barton'
GROUP BY author.id, author.first_name, author.last_name;
