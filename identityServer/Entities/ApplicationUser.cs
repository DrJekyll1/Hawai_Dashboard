using Microsoft.AspNetCore.Identity;

namespace IdentityServer.Entities
{
    public class ApplicationUser : IdentityUser
    {
        //public string FirstName { get; set; }
       // public Guid Id { get; set; }

        public ApplicationUser() { }
        public ApplicationUser(string FirstName) : base(FirstName) { }
    }
}
