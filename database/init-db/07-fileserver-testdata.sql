USE `fileserver`;


INSERT INTO `clients` (`id`, `name`, `preview_pic`, `short_description`, `redirect`)  VALUES
  ('05351551-0c65-43ba-880a-310526605816', 'Analyse Your Enterprise', 'http://localhost:5001/assets/images/noimagefound.jpg', 'Analyse your Enterprise with blabla', 'http://google.com'),
  ('ed256fdd-dc38-4225-88b7-aec5cc6d77fe', 'testClient', 'http://localhost:5001/assets/images/noimagefound.jpg', 'testing the functions', 'http://localhost:4201'),
  (3, 'dummy3', 'http://localhost:5001/assets/images/noimagefound.jpg', 'dritter MS Testdummy','yahoo.com');

INSERT INTO `clients_tags` (`id`, `client_id`, `tag_id`) VALUES
  ('0eee8fde-0cc8-4f6a-891e-3792be5cffca', 'ed256fdd-dc38-4225-88b7-aec5cc6d77fe', 'f6a5d2a37b484567bac18d591be4f383');


INSERT INTO `tags` (`id`, `name`) VALUES
  ('f6a5d2a37b484567bac18d591be4f383', 'aye');


