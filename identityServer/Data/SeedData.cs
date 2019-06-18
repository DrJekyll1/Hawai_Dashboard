using System;
using System.Linq;
using System.Security.Claims;
using IdentityModel;
using IdentityServer4.EntityFramework.DbContexts;
using IdentityServer4.EntityFramework.Mappers;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;

using IdentityServer4.EntityFramework;
using IdentityServer.Entities;

namespace IdentityServer.Data
{
    public class SeedData
    {
        public static void EnsureSeedData(IServiceProvider serviceProvider)
        {
            Console.WriteLine("Seeding database...");

            using (var scope = serviceProvider.GetRequiredService<IServiceScopeFactory>().CreateScope())
            {

                scope.ServiceProvider.GetRequiredService<PersistedGrantDbContext>().Database.Migrate();

                {
                    var context = scope.ServiceProvider.GetRequiredService<ConfigurationDbContext>();
                    context.Database.Migrate();
                    EnsureSeedData(context);
                }

                {
                    var context = scope.ServiceProvider.GetService<ApplicationDbContext>();
                    context.Database.Migrate();

                    IdentityResult roleResult;
                    var userMgr = scope.ServiceProvider.GetRequiredService<UserManager<ApplicationUser>>();

                    var RoleManager = scope.ServiceProvider.GetRequiredService<RoleManager<IdentityRole>>();

                    var roleCheck = RoleManager.RoleExistsAsync("Admin").Result;  
                    if (!roleCheck)  
                    {  
                        //create the roles and seed them to the database  
                        roleResult = RoleManager.CreateAsync(new IdentityRole("Admin")).Result;
                        if (!roleResult.Succeeded)
                        {
                            throw new Exception(roleResult.Errors.First().Description);
                        }
                    }

                    roleCheck = RoleManager.RoleExistsAsync("Employee").Result;
                    if (!roleCheck)
                    {
                        //create the roles and seed them to the database  
                        roleResult = RoleManager.CreateAsync(new IdentityRole("Employee")).Result;
                        if (!roleResult.Succeeded)
                        {
                            throw new Exception(roleResult.Errors.First().Description);
                        }
                    }

                    roleCheck = RoleManager.RoleExistsAsync("User").Result;
                    if (!roleCheck)
                    {
                        //create the roles and seed them to the database  
                        roleResult = RoleManager.CreateAsync(new IdentityRole("User")).Result;
                        if (!roleResult.Succeeded)
                        {
                            throw new Exception(roleResult.Errors.First().Description);
                        }
                    }
                                                     
                    var admin = userMgr.FindByNameAsync("Admin").Result;
                    if (admin == null)
                    {
                        admin = new ApplicationUser
                        {
                            UserName = "admin"
                        };
                        var result = userMgr.CreateAsync(admin, "AdminPassword2018$").Result;
                        if (!result.Succeeded)
                        {
                            throw new Exception(result.Errors.First().Description);
                        }

                        result = userMgr.AddToRoleAsync(admin, "Admin").Result;
                        if (!result.Succeeded)
                        {
                            throw new Exception(result.Errors.First().Description);
                        }

                        result = userMgr.AddClaimsAsync(admin, new Claim[]{
                            new Claim(JwtClaimTypes.Name, "Admin Hawai"),
                            new Claim(JwtClaimTypes.GivenName, "Admin"),
                            new Claim(JwtClaimTypes.FamilyName, "Hawai"),
                            new Claim(JwtClaimTypes.Email, "admin@email.com"),
                            new Claim(JwtClaimTypes.EmailVerified, "true", ClaimValueTypes.Boolean),
                            new Claim(JwtClaimTypes.Role, "Admin"),
                            new Claim(JwtClaimTypes.Address, @"{ 'street_address': 'Berliner Tor', 'locality': 'Hamburg', 'postal_code': 223455, 'country': 'Germany' }", IdentityServer4.IdentityServerConstants.ClaimValueTypes.Json)
                        }).Result;
                        if (!result.Succeeded)
                        {
                            throw new Exception(result.Errors.First().Description);
                        }


                        Console.WriteLine("admin created");
                    }
                    else
                    {
                        Console.WriteLine("admin already exists");
                    }

                }
            }

            Console.WriteLine("Done seeding database.");
            Console.WriteLine();
        }

        private static void EnsureSeedData(ConfigurationDbContext context)
        {
            if (!context.Clients.Any())
            {
                Console.WriteLine("Clients being populated");
                foreach (var client in Config.GetClients().ToList())
                {
                    Console.Write(client);
                    context.Clients.Add(client.ToEntity());
                }
                context.SaveChanges();
            }
            else
            {
                Console.WriteLine("Clients already populated");
            }

            if (!context.IdentityResources.Any())
            {
                Console.WriteLine("IdentityResources being populated");
                foreach (var resource in Config.GetIdentityResources().ToList())
                {
                    context.IdentityResources.Add(resource.ToEntity());
                }
                context.SaveChanges();
            }
            else
            {
                Console.WriteLine("IdentityResources already populated");
            }

            if (!context.ApiResources.Any())
            {
                Console.WriteLine("ApiResources being populated");
                foreach (var resource in Config.GetApiResources().ToList())
                {
                    context.ApiResources.Add(resource.ToEntity());
                }
                context.SaveChanges();
            }
            else
            {
                Console.WriteLine("ApiResources already populated");
            }
        }
    }
}
