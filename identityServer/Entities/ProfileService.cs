using IdentityServer4.Services;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using IdentityServer4.Models;
using Microsoft.AspNetCore.Identity;
using IdentityServer4.Extensions;
using System.Security.Claims;
using IdentityModel;

namespace IdentityServer.Entities
{
    public class ProfileService : IProfileService
    {
        private readonly IUserClaimsPrincipalFactory<ApplicationUser> _claimsFactory;
        private readonly UserManager<ApplicationUser> _userManager;

        public ProfileService(UserManager<ApplicationUser> userManager, IUserClaimsPrincipalFactory<ApplicationUser> claimsFactory)
        {
            _claimsFactory = claimsFactory;
            _userManager = userManager;
        }

        public async Task GetProfileDataAsync(ProfileDataRequestContext context)
        {
            var sub = context.Subject.GetSubjectId();
            var user = await _userManager.FindByIdAsync(sub);
            var claimn = await _userManager.GetClaimsAsync(user);
            var principal = await _claimsFactory.CreateAsync(user);

            var claims = principal.Claims.ToList();
            
           claims = claims.Where(claim => context.RequestedClaimTypes.Contains(claim.Type)).ToList();
          
            if ( context.Caller == "UserInfoEndpoint")
            {
                // claims.Add(new Claim(JwtClaimTypes.GivenName, claims..)); // , user.FirstName));
            }
            //if (context.Caller == "ClaimsProviderAccessToken")
            //{
            //    claims.Add(new Claim(JwtClaimTypes.Email, user.Email));
            //}
            

            context.IssuedClaims = claims;

        }

        public async Task IsActiveAsync(IsActiveContext context)
        {
            var sub = context.Subject.GetSubjectId();
            var user = await _userManager.FindByIdAsync(sub);
            context.IsActive = user != null;
        }
    }
}
