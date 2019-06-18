﻿using Microsoft.AspNetCore.Identity;

namespace IdentityServer.Entities
{
    public class ApplicationRole : IdentityRole
    {
       
        public ApplicationRole() { }
        public ApplicationRole(string roleName) : base(roleName)
        {
        }
    }
}
