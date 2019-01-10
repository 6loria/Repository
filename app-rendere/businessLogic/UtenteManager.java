package businessLogic;

import java.util.logging.Level;
import java.util.logging.Logger;

import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;

import org.eclipse.persistence.internal.jpa.EntityManagerFactoryProvider;

import project.Utente;
import utility.JPAutility;

public class UtenteManager {
private static Logger log = Logger.getLogger("app-rendere");
	
	public static void aggiungiUtente(Utente u) {
		EntityManager em = JPAutility.getEm();
		Utente uDb = null;
		if(u.getEmail() != null) {
			uDb = em.find(Utente.class, u.getEmail());
		}
		if (uDb == null) {
			em.getTransaction().begin();
			em.persist(u); 
			em.getTransaction().commit();
			log.log(Level.INFO, "utente aggiunto");
		} else {
			log.log(Level.WARNING, "utente gi� esistente");
		}
			
		}
	
		public void rimuoviUtente(Utente u) {
			EntityManager em = JPAutility.getEm();
			Utente uDb = em.find(Utente.class, u.getEmail());
			if (uDb = null) {
				em.getTransaction().begin();
				uDb.setAttivo(u.getRemove());
				em.getTransaction().commit();
				log.log(Level.INFO, "utente rimosso");
			}

		}

		public static void modificaUtente(Utente u) {
			EntityManager em = JPAutility.getEm();
			Utente uDb = em.find(Utente.class, u.getEmail());
			if (uDb != null) {
				em.getTransaction().begin();
				uDb.setAttivo(u.getAttivo());
				em.getTransaction().commit();
				log.log(Level.INFO, "utente modificato");
	

			}
		}
		
		public static void abilitaUtente(Utente u) {
			EntityManager em = JPAutility.getEm();
			Utente uDb = null;
			if(u.getAttivo() != null) {
				uDb = em.find(Utente.class, u.getAttivo());
			}
			if (uDb == null) {
				em.getTransaction().begin();
				em.persist(u); 
				em.getTransaction().commit();
				log.log(Level.INFO, "utente attivo");
			} else {
				log.log(Level.WARNING, "utente non attivo");
			}
		}
}
